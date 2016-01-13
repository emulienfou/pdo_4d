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
#include <fourd.h>
#include <ext/mbstring/mbstring.h>
#define FOURD_CHARSET_SERVEUR "UTF-16LE"

typedef struct {
	const char *file;
	int line;
	unsigned int errcode;
	char *errmsg;
} pdo_4d_error_info;

/* stuff we use in a mySQL database handle */
typedef struct {
	FOURD 		*server;

	unsigned attached:1;
	unsigned buffered:1;
	unsigned emulate_prepare:1;
	unsigned _reserved:31;
	unsigned long max_buffer_size;

	pdo_4d_error_info einfo;
	char* charset;
} pdo_4d_db_handle;


typedef struct {
	FOURD_ELEMENT		*def;
} pdo_fourd_column;

typedef struct {
	pdo_4d_db_handle 	*H;
	FOURD_STATEMENT *state;	
	FOURD_RESULT		*result;
	FOURD_ELEMENT	    *fields;
	int		current_row;
	pdo_4d_error_info einfo;
	char* charset;
	//int num_params;
} pdo_4d_stmt;

extern int _pdo_4d_error(pdo_dbh_t *dbh, pdo_stmt_t *stmt, const char *file, int line TSRMLS_DC);
#define pdo_4d_error(s) _pdo_4d_error(s, NULL, __FILE__, __LINE__ TSRMLS_CC)
#define pdo_4d_error_stmt(s) _pdo_4d_error(stmt->dbh, stmt, __FILE__, __LINE__ TSRMLS_CC)

extern struct pdo_stmt_methods fourd_stmt_methods;


enum {
	PDO_FOURD_ATTR_CHARSET = PDO_ATTR_DRIVER_SPECIFIC,
	PDO_FOURD_ATTR_PREFERRED_IMAGE_TYPES,
	
};