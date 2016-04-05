dnl
dnl $ Id: $
dnl

PHP_ARG_WITH(pdo-4d, for 4D-SQL support for PDO,
[  --with-pdo-4d         PDO: 4D-SQL support])

if test "$PHP_PDO_4D" != "no"; then
  dnl #export OLD_CPPFLAGS="$CPPFLAGS"
  dnl #export CPPFLAGS="$CPPFLAGS $INCLUDES -DHAVE_PDO_4D"
  
	ifdef(
	  [PHP_CHECK_PDO_INCLUDES],
	  [PHP_CHECK_PDO_INCLUDES],
	  [
	    AC_MSG_CHECKING([for PDO includes])
	    if test -f $abs_srcdir/include/php/ext/pdo/php_pdo_driver.h; then
	      pdo_inc_path=$abs_srcdir/ext
	    elif test -f $abs_srcdir/ext/pdo/php_pdo_driver.h; then
	      pdo_inc_path=$abs_srcdir/ext
	    elif test -f $prefix/include/php/ext/pdo/php_pdo_driver.h; then
	      pdo_inc_path=$prefix/include/php/ext
	    elif test -f $phpincludedir/ext/pdo/php_pdo_driver.h; then
	      pdo_inc_path=$phpincludedir/ext
	    else
	      AC_MSG_ERROR([Cannot find php_pdo_driver.h.])
	    fi
	    AC_MSG_RESULT($pdo_inc_path)
	  ]
	)
	PHP_ADD_INCLUDE($pdo_inc_path)
	PHP_ADD_INCLUDE("$pdo_inc_path/pdo")
	
	if test "$PHP_PDO_4D" != "yes"; then
		PDO_4D_DIR=$PHP_PDO_4D
		PHP_ADD_INCLUDE("$PHP_PDO_4D/include")
		PHP_ADD_LIBRARY_WITH_PATH(4d_sql, "$PDO_4D_DIR/lib", PDO_4D_SHARED_LIBADD)
		
		AC_DEFINE(HAVE_PDO_4D,1,[ ])
		PHP_NEW_EXTENSION(pdo_4d, pdo_4d.c 4d_driver.c 4d_statement.c, $ext_shared,,-I$pdo_inc_path)
	else
		#use bundled lib4d_sql
		pdo_4d_sources=" pdo_4d.c 4d_driver.c 4d_statement.c"
		lib4d_sql_sources="lib4d_sql/base64.c lib4d_sql/communication.c lib4d_sql/fourd.c lib4d_sql/fourd_interne.c lib4d_sql/fourd_result.c lib4d_sql/fourd_type.c lib4d_sql/sqlstate.c lib4d_sql/utils.c"

		PHP_NEW_EXTENSION(pdo_4d, $lib4d_sql_sources $pdo_4d_sources, $ext_shared,,-I$pdo_inc_path -I@ext_srcdir@/lib4d_sql)
		PHP_ADD_BUILD_DIR($ext_builddir/lib4d_sql)
		PHP_ADD_INCLUDE("$ext_srcdir/lib4d_sql")
		
		AC_DEFINE(HAVE_PDO_4D,1,[ ])
	fi
    ifdef([PHP_ADD_EXTENSION_DEP],
  [
    PHP_ADD_EXTENSION_DEP(pdo_4d, pdo)
    PHP_ADD_EXTENSION_DEP(pdo_4d, mbstring)
  ])

dnl  #export CPPFLAGS="$OLD_CPPFLAGS"
fi
