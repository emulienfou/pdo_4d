/*
  +----------------------------------------------------------------------+
  | PECL :: PDO_4D                                                       |
  +----------------------------------------------------------------------+
  | Copyright (c) 2009 The PHP Group                                     |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.01 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_01.txt                                  |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  |                                                                      |
  | Unless required by applicable law or agreed to in writing, software  |
  | distributed under the License is distributed on an "AS IS" BASIS,    |
  | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or      |
  | implied. See the License for the specific language governing         |
  | permissions and limitations under the License.                       |
  +----------------------------------------------------------------------+
  | Contributed by: 4D <php@4d.fr>, http://www.4d.com                    |
  |                 Alter Way, http://www.alterway.fr                    |
  | Authors: Stephane Planquart <stephane.planquart@o4db.com>            |
  |          Alexandre Morgaut <php@4d.fr>                               |
  +----------------------------------------------------------------------+
*/

/* $ Id: $ */ 

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "pdo/php_pdo.h"
#include "pdo/php_pdo_driver.h"
#include "php_pdo_4d.h"
#include "php_pdo_4d_int.h"


static int pdo_4d_stmt_execute(pdo_stmt_t *stmt TSRMLS_DC)
{
	pdo_4d_stmt *S;
	pdo_4d_db_handle *H;
	FOURD_LONG8 row_count;
	S = (pdo_4d_stmt*)stmt->driver_data;
	H = S->H;
	if (S->result) {
		fourd_free_result(S->result);
		S->result = NULL;
	}
	if(S->state==NULL) {	/* if statement has not prepared */
		if ((S->result=fourd_query(H->server, stmt->active_query_string)) ==NULL) {
			pdo_4d_error_stmt(stmt);
			return 0;
		}
	}
	else {	/* if statement has prepared */
		if ((S->result=fourd_exec_statement(S->state)) ==NULL) {
			pdo_4d_error_stmt(stmt);
			return 0;
		}
	}
	if ((row_count = fourd_affected_rows(H->server)) == (FOURD_LONG8)-1) {
		
		stmt->row_count = fourd_num_rows(S->result);
		stmt->column_count = fourd_num_columns(S->result);
	} 
	else {
		/* this was a DML or DDL query (INSERT, UPDATE, DELETE, ... */
		stmt->row_count = row_count;
	}
	return 1;
}

static int pdo_4d_stmt_describe(pdo_stmt_t *stmt, int colno TSRMLS_DC)
{
	pdo_4d_stmt *S = (pdo_4d_stmt*)stmt->driver_data;
	struct pdo_column_data *cols = stmt->columns;
	unsigned int i;

	if (!S->result) {
		return 0;	
	}

	if (colno >= stmt->column_count) {
		/* error invalid column */
		return 0;
	}

	/* fetch all on demand, this seems easiest 
	** if we've been here before bail out 
	*/
	if (cols[0].name) {
		return 1;
	}
	for (i=0; i < stmt->column_count; i++) {
		int namelen;
		const char *name=fourd_get_column_name(S->result,i);
		namelen = strlen(name);
		cols[i].precision = 0;
		cols[i].maxlen = 0;/*namelen;*/
#if PHP_VERSION_ID >= 70000
		cols[i].name = zend_string_init(name, namelen, 0);
#else
		cols[i].namelen = namelen;
		cols[i].name = estrndup(name, namelen);
#endif
		cols[i].param_type = PDO_PARAM_STR;
		//if(i==1) cols[i].param_type = PDO_PARAM_LOB;
	}
	return 1;
}

static int pdo_4d_stmt_get_col(pdo_stmt_t *stmt, int colno, char **ptr, unsigned long *len, int *caller_frees TSRMLS_DC)
{
	pdo_4d_stmt *S = (pdo_4d_stmt*)stmt->driver_data;

	if (!S->result) {
		return 0;
	}
	if (colno >= stmt->column_count) {
		/* error invalid column */
		return 0;
	}
	fourd_field_to_string(S->result,colno,ptr,len);
	
	switch(fourd_get_column_type(S->result,colno))
	{
		case VK_STRING:
			/* convert into desired charset */
			*ptr=php_mb_convert_encoding(*ptr, *len,S->charset,FOURD_CHARSET_SERVEUR,len TSRMLS_CC);
			break;
		case VK_BLOB:
		case VK_IMAGE:
			/*use emalloc for memory allocation and free memory allocate by malloc */
			{
				char *lptr=NULL;
				FOURD_BLOB *b=NULL;
				b=fourd_field(S->result,colno);
				if(b!=NULL){
					free(*ptr);	/* fourd_get_column_type return "" for blob or image type resource*/
					*ptr=emalloc(b->length);
					*len=b->length;
					memcpy(*ptr,b->data,b->length);
				}
				else {
					*ptr=NULL;
					*len=0;
				}
			}
			break;
		default:
			/* convert into desired charset from "ISO-8859-1" for not VK_STRING,VK_BLOB or VK_IMAGE data */
			*ptr=php_mb_convert_encoding(*ptr, *len,S->charset,"ISO-8859-1",len TSRMLS_CC);
			break;
  }
	
	return 1;
}
static int pdo_4d_stmt_fetch(pdo_stmt_t *stmt, enum pdo_fetch_orientation ori, long offset TSRMLS_DC)
{
	pdo_4d_stmt *S = (pdo_4d_stmt*)stmt->driver_data;

	if (!S->result) {
		strcpy(stmt->error_code, "HY000");
		return 0;	
	}
	if (!fourd_next_row(S->result)) {
		if (fourd_errno(S->H->server)>0) {
			pdo_4d_error_stmt(stmt);
		}
		return 0;
	} 
	//S->current_lengths = fourd_fetch_lengths(S->result);
	return 1;	
}
static int pdo_4d_stmt_set_attribute(pdo_stmt_t *stmt, long attr, zval *val TSRMLS_DC)
{
	pdo_4d_stmt *S = (pdo_4d_stmt*)stmt->driver_data;
	pdo_4d_db_handle *H = S->H;
	switch (attr) {
	case PDO_FOURD_ATTR_CHARSET:
		S->charset = pestrdup(Z_STRVAL_P(val),1);
		return 1;
	default:
		return 0;
	}
}
static int pdo_4d_stmt_get_attribute(pdo_stmt_t *stmt, long attr, zval *return_value TSRMLS_DC)
{
	//pdo_4d_db_handle *H = (pdo_4d_db_handle *)dbh->driver_data;
	pdo_4d_stmt *S = (pdo_4d_stmt*)stmt->driver_data;
	switch (attr) {
		case PDO_FOURD_ATTR_CHARSET:
#if PHP_VERSION_ID >= 70000
			ZVAL_STRING(return_value, S->charset);
#else
			ZVAL_STRING(return_value, S->charset, 1);
#endif
			break;
		default:
			return 0;	
	}

	return 1;
}
static int pdo_4d_stmt_col_meta(pdo_stmt_t *stmt, long colno, zval *return_value TSRMLS_DC)
{
	pdo_4d_stmt *S = (pdo_4d_stmt*)stmt->driver_data;
	//MYSQL_FIELD *F;
#if PHP_VERSION_ID >= 70000
	zval flags;
#else
	zval *flags;
#endif
	char *str;
	FOURD_TYPE type;
	
	if (!S->result) {
		return FAILURE;
	}
	if (colno >= stmt->column_count) {
		/* error invalid column */
		return FAILURE;
	}

	array_init(return_value);
#if PHP_VERSION_ID < 70000
	MAKE_STD_ZVAL(flags);
	array_init(flags);
#else
	array_init(&flags);
#endif
	

	switch(type=fourd_get_column_type(S->result,colno))
	{
		case VK_BLOB:
		case VK_IMAGE:
#if PHP_VERSION_ID >= 70000
			add_next_index_string(&flags, "blob");
#else
			add_next_index_string(flags, "blob", 1);
#endif
			
			break;
	}
	str=pestrdup(stringFromType(type),1);
	if (str) {
#if PHP_VERSION_ID >= 70000
		add_assoc_string(return_value, "native_type", str);
#else
		add_assoc_string(return_value, "native_type", str, 1);
#endif
	}

#if PHP_VERSION_ID >= 70000
	add_assoc_zval(return_value, "flags", &flags);
#else
	add_assoc_zval(return_value, "flags", flags);
#endif
	
	return SUCCESS;
}
static int pdo_4d_stmt_cursor_closer(pdo_stmt_t *stmt TSRMLS_DC)
{
	pdo_4d_stmt *S = (pdo_4d_stmt*)stmt->driver_data;
	
	if (S->result) {
		fourd_close_statement(S->result);
		fourd_free_result(S->result);
		S->result = NULL;
	}
	return 1;
}
static int pdo_4d_stmt_dtor(pdo_stmt_t *stmt TSRMLS_DC)
{
	pdo_4d_stmt *S = (pdo_4d_stmt*)stmt->driver_data;

	if (S->result) {
		/* free the resource */
		fourd_close_statement(S->result);
		fourd_free_result(S->result);
		S->result = NULL;
	}
	if (S->einfo.errmsg) {
		pefree(S->einfo.errmsg, stmt->dbh->is_persistent);
		S->einfo.errmsg = NULL;
	}
	efree(S);
	return 1;
}
static int pdo_4d_stmt_param_hook(pdo_stmt_t *stmt, struct pdo_bound_param_data *param,
		enum pdo_param_event event_type TSRMLS_DC)
{
	pdo_4d_stmt *S = (pdo_4d_stmt*)stmt->driver_data;
	if(S->state == NULL) { /* it's not a prepared statement */
		return 1;	
	}
	switch (event_type) {
		
		case PDO_PARAM_EVT_NORMALIZE:
/*			if (param->name) {
				if (param->name[0] == '$') {
					param->paramno = atoi(param->name + 1);
				} else {
					char *nameptr;
					if (stmt->bound_param_map && SUCCESS == zend_hash_find(stmt->bound_param_map,
							param->name, param->namelen + 1, (void**)&nameptr)) {
						param->paramno = atoi(nameptr + 1) - 1;
					} else {
						fprintf(stderr,"Error HY093\n");
						pdo_raise_impl_error(stmt->dbh, stmt, "HY093", param->name TSRMLS_CC);
						return 0;
					}
				}
			}*/
			/*printf("bind param:%d\n",param->paramno);*/
			if (param->paramno < 0 ) {
				strcpy(stmt->error_code, "HY093");
#if PHP_VERSION_ID >= 70000
				pdo_raise_impl_error(stmt->dbh, stmt, "HY093", param->name->val TSRMLS_CC);
#else
				pdo_raise_impl_error(stmt->dbh, stmt, "HY093", param->name TSRMLS_CC);
#endif
				return 0;
			}
			return 1;
			break;
		case PDO_PARAM_EVT_EXEC_PRE:
		/* printf("bind_param: %d,%d\n",PDO_PARAM_TYPE(param->param_type),Z_TYPE_P(param->parameter)); */
			if (param->paramno < 0 ) {
				strcpy(stmt->error_code, "HY093");
#if PHP_VERSION_ID >= 70000
				pdo_raise_impl_error(stmt->dbh, stmt, "HY093", param->name->val TSRMLS_CC);
#else
				pdo_raise_impl_error(stmt->dbh, stmt, "HY093", param->name TSRMLS_CC);
#endif
				return 0;
			}
			if (param->is_param) {
				switch (PDO_PARAM_TYPE(param->param_type)) {
					case PDO_PARAM_STMT:
						return 0;

					case PDO_PARAM_NULL:
						fourd_bind_param(S->state,param->paramno,VK_STRING, NULL);
						return 1;
					case PDO_PARAM_INT:
						{
#if PHP_VERSION_ID >= 70000
							FOURD_LONG val=Z_LVAL(param->parameter);
#else
							FOURD_LONG val=Z_LVAL_P(param->parameter);
#endif
							fourd_bind_param(S->state,param->paramno,VK_LONG, &val);
						}
						return 1;

					case PDO_PARAM_LOB:
#if PHP_VERSION_ID >= 70000
						if (Z_TYPE(param->parameter) == IS_RESOURCE) {
#else
						if (Z_TYPE_P(param->parameter) == IS_RESOURCE) {
#endif
							php_stream *stm;
							php_stream_from_zval_no_verify(stm, &param->parameter);
							if (stm) {
								SEPARATE_ZVAL(&param->parameter);
#if PHP_VERSION_ID >= 70000
								param->parameter.u1.v.type = IS_STRING;
								Z_STRLEN(param->parameter) = php_stream_copy_to_mem(stm,
									PHP_STREAM_COPY_ALL, 0)->len;
#else
								Z_TYPE_P(param->parameter) = IS_STRING;
								Z_STRLEN_P(param->parameter) = php_stream_copy_to_mem(stm,
									&Z_STRVAL_P(param->parameter), PHP_STREAM_COPY_ALL, 0);
#endif
							} else {
								pdo_raise_impl_error(stmt->dbh, stmt, "HY105", "Expected a stream resource" TSRMLS_CC);
								return 0;
							}
							{
								FOURD_BLOB str;
								int len=0;
#if PHP_VERSION_ID >= 70000
								str.length=Z_STRLEN(param->parameter);	
								str.data=Z_STRVAL(param->parameter);
#else
								str.length=Z_STRLEN_P(param->parameter);	
								str.data=Z_STRVAL_P(param->parameter);
#endif
								fourd_bind_param(S->state,param->paramno,VK_BLOB, &str);
							}
							return 1;
						}
						/* fall through */
					case PDO_PARAM_STR:
					default:
#if PHP_VERSION_ID >= 70000
						switch (Z_TYPE(param->parameter)) {
#else
						switch (Z_TYPE_P(param->parameter)) {
#endif
							case IS_NULL:
								fourd_bind_param(S->state,param->paramno,VK_STRING, NULL);
								return 1;
							case IS_LONG:
								{
#if PHP_VERSION_ID >= 70000
									FOURD_LONG val=Z_LVAL(param->parameter);
#else
									FOURD_LONG val=Z_LVAL_P(param->parameter);
#endif
									fourd_bind_param(S->state,param->paramno,VK_LONG, &val);
								}
								return 1;
							case IS_DOUBLE:
#if PHP_VERSION_ID >= 70000
								fourd_bind_param(S->state,param->paramno,VK_REAL, &Z_DVAL(param->parameter));
#else
								fourd_bind_param(S->state,param->paramno,VK_REAL, &Z_DVAL_P(param->parameter));
#endif
								return 1;
							case IS_STRING:
								{									
									FOURD_STRING str;
									int len=0;
									/*  MBSTRING_API char * php_mb_convert_encoding(char *input, size_t length, char *_to_encoding, char *_from_encodings, size_t *output_len TSRMLS_DC) */
#if PHP_VERSION_ID >= 70000
									char* val=php_mb_convert_encoding(Z_STRVAL(param->parameter), Z_STRLEN(param->parameter),FOURD_CHARSET_SERVEUR,S->charset,&len TSRMLS_CC);
#else
									char* val=php_mb_convert_encoding(Z_STRVAL_P(param->parameter), Z_STRLEN_P(param->parameter),FOURD_CHARSET_SERVEUR,S->charset,&len TSRMLS_CC);
#endif
									str.length=len/2;	
									str.data=val;
									fourd_bind_param(S->state,param->paramno,VK_STRING, &str);
								}
								return 1;
							default:
								{
									FOURD_STRING str;
									int len=0;
									char* val=NULL;
#if PHP_VERSION_ID >= 70000
									convert_to_string(&param->parameter);
									val=php_mb_convert_encoding(Z_STRVAL(param->parameter), Z_STRLEN(param->parameter),FOURD_CHARSET_SERVEUR,S->charset,&len TSRMLS_CC);
#else
									convert_to_string(param->parameter);
									val=php_mb_convert_encoding(Z_STRVAL_P(param->parameter), Z_STRLEN_P(param->parameter),FOURD_CHARSET_SERVEUR,S->charset,&len TSRMLS_CC);
#endif
									str.length=len/2;
									str.data=val;
									fourd_bind_param(S->state,param->paramno,VK_STRING, &str);
								}
								return 1;

						}
				}
			}
			break;
		default:
			;
	}
	return 1;
}
struct pdo_stmt_methods fourd_stmt_methods = {
	pdo_4d_stmt_dtor,		
	pdo_4d_stmt_execute,	
	pdo_4d_stmt_fetch,		
	pdo_4d_stmt_describe,	
	pdo_4d_stmt_get_col,	
	pdo_4d_stmt_param_hook,	
	pdo_4d_stmt_set_attribute,
	pdo_4d_stmt_get_attribute,
	pdo_4d_stmt_col_meta,
	NULL,				
	pdo_4d_stmt_cursor_closer,	
};
