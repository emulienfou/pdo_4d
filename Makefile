srcdir = /home/edv-administrator/pdo4d
builddir = /home/edv-administrator/pdo4d
top_srcdir = /home/edv-administrator/pdo4d
top_builddir = /home/edv-administrator/pdo4d
EGREP = /bin/grep -E
SED = /bin/sed
CONFIGURE_COMMAND = './configure' '--with-pdo-4d'
CONFIGURE_OPTIONS = '--with-pdo-4d'
SHLIB_SUFFIX_NAME = so
SHLIB_DL_SUFFIX_NAME = so
ZEND_EXT_TYPE = zend_extension
RE2C = exit 0;
AWK = gawk
shared_objects_pdo_4d = lib4d_sql/base64.lo lib4d_sql/communication.lo lib4d_sql/fourd.lo lib4d_sql/fourd_interne.lo lib4d_sql/fourd_result.lo lib4d_sql/fourd_type.lo lib4d_sql/sqlstate.lo lib4d_sql/utils.lo pdo_4d.lo 4d_driver.lo 4d_statement.lo
PHP_PECL_EXTENSION = pdo_4d
PHP_MODULES = $(phplibdir)/pdo_4d.la
PHP_ZEND_EX =
all_targets = $(PHP_MODULES) $(PHP_ZEND_EX)
install_targets = install-modules install-headers
prefix = /usr
exec_prefix = $(prefix)
libdir = ${exec_prefix}/lib
prefix = /usr
phplibdir = /home/edv-administrator/pdo4d/modules
phpincludedir = /usr/include/php5
CC = cc
CFLAGS = -g -O2
CFLAGS_CLEAN = $(CFLAGS)
CPP = cc -E
CPPFLAGS = -DHAVE_CONFIG_H
CXX =
CXXFLAGS =
CXXFLAGS_CLEAN = $(CXXFLAGS)
EXTENSION_DIR = /usr/lib/php5/20090626+lfs
PHP_EXECUTABLE = /usr/bin/php
EXTRA_LDFLAGS =
EXTRA_LIBS =
INCLUDES = -I/usr/include/php5 -I/usr/include/php5/main -I/usr/include/php5/TSRM -I/usr/include/php5/Zend -I/usr/include/php5/ext -I/usr/include/php5/ext/date/lib -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64 -I/usr/include/php/ext -I/usr/include/php/ext/pdo -I./lib4d_sql
LFLAGS =
LDFLAGS =
SHARED_LIBTOOL =
LIBTOOL = $(SHELL) $(top_builddir)/libtool
SHELL = /bin/bash
INSTALL_HEADERS =
mkinstalldirs = $(top_srcdir)/build/shtool mkdir -p
INSTALL = $(top_srcdir)/build/shtool install -c
INSTALL_DATA = $(INSTALL) -m 644

DEFS = -DPHP_ATOM_INC -I$(top_builddir)/include -I$(top_builddir)/main -I$(top_srcdir)
COMMON_FLAGS = $(DEFS) $(INCLUDES) $(EXTRA_INCLUDES) $(CPPFLAGS) $(PHP_FRAMEWORKPATH)

all: $(all_targets) 
	@echo
	@echo "Build complete."
	@echo "Don't forget to run 'make test'."
	@echo
	
build-modules: $(PHP_MODULES) $(PHP_ZEND_EX)

libphp$(PHP_MAJOR_VERSION).la: $(PHP_GLOBAL_OBJS) $(PHP_SAPI_OBJS)
	$(LIBTOOL) --mode=link $(CC) $(CFLAGS) $(EXTRA_CFLAGS) -rpath $(phptempdir) $(EXTRA_LDFLAGS) $(LDFLAGS) $(PHP_RPATHS) $(PHP_GLOBAL_OBJS) $(PHP_SAPI_OBJS) $(EXTRA_LIBS) $(ZEND_EXTRA_LIBS) -o $@
	-@$(LIBTOOL) --silent --mode=install cp $@ $(phptempdir)/$@ >/dev/null 2>&1

libs/libphp$(PHP_MAJOR_VERSION).bundle: $(PHP_GLOBAL_OBJS) $(PHP_SAPI_OBJS)
	$(CC) $(MH_BUNDLE_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS) $(LDFLAGS) $(EXTRA_LDFLAGS) $(PHP_GLOBAL_OBJS:.lo=.o) $(PHP_SAPI_OBJS:.lo=.o) $(PHP_FRAMEWORKS) $(EXTRA_LIBS) $(ZEND_EXTRA_LIBS) -o $@ && cp $@ libs/libphp$(PHP_MAJOR_VERSION).so

install: $(all_targets) $(install_targets)

install-sapi: $(OVERALL_TARGET)
	@echo "Installing PHP SAPI module:       $(PHP_SAPI)"
	-@$(mkinstalldirs) $(INSTALL_ROOT)$(bindir)
	-@if test ! -r $(phptempdir)/libphp$(PHP_MAJOR_VERSION).$(SHLIB_DL_SUFFIX_NAME); then \
		for i in 0.0.0 0.0 0; do \
			if test -r $(phptempdir)/libphp$(PHP_MAJOR_VERSION).$(SHLIB_DL_SUFFIX_NAME).$$i; then \
				$(LN_S) $(phptempdir)/libphp$(PHP_MAJOR_VERSION).$(SHLIB_DL_SUFFIX_NAME).$$i $(phptempdir)/libphp$(PHP_MAJOR_VERSION).$(SHLIB_DL_SUFFIX_NAME); \
				break; \
			fi; \
		done; \
	fi
	@$(INSTALL_IT)

install-modules: build-modules
	@test -d modules && \
	$(mkinstalldirs) $(INSTALL_ROOT)$(EXTENSION_DIR)
	@echo "Installing shared extensions:     $(INSTALL_ROOT)$(EXTENSION_DIR)/"
	@rm -f modules/*.la >/dev/null 2>&1
	@$(INSTALL) modules/* $(INSTALL_ROOT)$(EXTENSION_DIR)

install-headers:
	-@if test "$(INSTALL_HEADERS)"; then \
		for i in `echo $(INSTALL_HEADERS)`; do \
			i=`$(top_srcdir)/build/shtool path -d $$i`; \
			paths="$$paths $(INSTALL_ROOT)$(phpincludedir)/$$i"; \
		done; \
		$(mkinstalldirs) $$paths && \
		echo "Installing header files:          $(INSTALL_ROOT)$(phpincludedir)/" && \
		for i in `echo $(INSTALL_HEADERS)`; do \
			if test "$(PHP_PECL_EXTENSION)"; then \
				src=`echo $$i | $(SED) -e "s#ext/$(PHP_PECL_EXTENSION)/##g"`; \
			else \
				src=$$i; \
			fi; \
			if test -f "$(top_srcdir)/$$src"; then \
				$(INSTALL_DATA) $(top_srcdir)/$$src $(INSTALL_ROOT)$(phpincludedir)/$$i; \
			elif test -f "$(top_builddir)/$$src"; then \
				$(INSTALL_DATA) $(top_builddir)/$$src $(INSTALL_ROOT)$(phpincludedir)/$$i; \
			else \
				(cd $(top_srcdir)/$$src && $(INSTALL_DATA) *.h $(INSTALL_ROOT)$(phpincludedir)/$$i; \
				cd $(top_builddir)/$$src && $(INSTALL_DATA) *.h $(INSTALL_ROOT)$(phpincludedir)/$$i) 2>/dev/null || true; \
			fi \
		done; \
	fi

PHP_TEST_SETTINGS = -d 'open_basedir=' -d 'output_buffering=0' -d 'memory_limit=-1'
PHP_TEST_SHARED_EXTENSIONS =  ` \
	if test "x$(PHP_MODULES)" != "x"; then \
		for i in $(PHP_MODULES)""; do \
			. $$i; $(top_srcdir)/build/shtool echo -n -- " -d extension=$$dlname"; \
		done; \
	fi; \
	if test "x$(PHP_ZEND_EX)" != "x"; then \
		for i in $(PHP_ZEND_EX)""; do \
			. $$i; $(top_srcdir)/build/shtool echo -n -- " -d $(ZEND_EXT_TYPE)=$(top_builddir)/modules/$$dlname"; \
		done; \
	fi`
PHP_DEPRECATED_DIRECTIVES_REGEX = '^(define_syslog_variables|register_(globals|long_arrays)?|safe_mode|magic_quotes_(gpc|runtime|sybase)?|(zend_)?extension(_debug)?(_ts)?)[\t\ ]*='

test: all
	-@if test ! -z "$(PHP_EXECUTABLE)" && test -x "$(PHP_EXECUTABLE)"; then \
		INI_FILE=`$(PHP_EXECUTABLE) -d 'display_errors=stderr' -r 'echo php_ini_loaded_file();' 2> /dev/null`; \
		if test "$$INI_FILE"; then \
			$(EGREP) -h -v $(PHP_DEPRECATED_DIRECTIVES_REGEX) "$$INI_FILE" > $(top_builddir)/tmp-php.ini; \
		else \
			echo > $(top_builddir)/tmp-php.ini; \
		fi; \
		INI_SCANNED_PATH=`$(PHP_EXECUTABLE) -d 'display_errors=stderr' -r '$$a = explode(",\n", trim(php_ini_scanned_files())); echo $$a[0];' 2> /dev/null`; \
		if test "$$INI_SCANNED_PATH"; then \
			INI_SCANNED_PATH=`$(top_srcdir)/build/shtool path -d $$INI_SCANNED_PATH`; \
			$(EGREP) -h -v $(PHP_DEPRECATED_DIRECTIVES_REGEX) "$$INI_SCANNED_PATH"/*.ini >> $(top_builddir)/tmp-php.ini; \
		fi; \
		TEST_PHP_EXECUTABLE=$(PHP_EXECUTABLE) \
		TEST_PHP_SRCDIR=$(top_srcdir) \
		CC="$(CC)" \
			$(PHP_EXECUTABLE) -n -c $(top_builddir)/tmp-php.ini $(PHP_TEST_SETTINGS) $(top_srcdir)/run-tests.php -n -c $(top_builddir)/tmp-php.ini -d extension_dir=$(top_builddir)/modules/ $(PHP_TEST_SHARED_EXTENSIONS) $(TESTS); \
		rm $(top_builddir)/tmp-php.ini; \
	else \
		echo "ERROR: Cannot run tests without CLI sapi."; \
	fi

clean:
	find . -name \*.gcno -o -name \*.gcda | xargs rm -f
	find . -name \*.lo -o -name \*.o | xargs rm -f
	find . -name \*.la -o -name \*.a | xargs rm -f 
	find . -name \*.so | xargs rm -f
	find . -name .libs -a -type d|xargs rm -rf
	rm -f libphp$(PHP_MAJOR_VERSION).la $(SAPI_CLI_PATH) $(OVERALL_TARGET) modules/* libs/*

distclean: clean
	rm -f Makefile config.cache config.log config.status Makefile.objects Makefile.fragments libtool main/php_config.h stamp-h sapi/apache/libphp$(PHP_MAJOR_VERSION).module buildmk.stamp
	$(EGREP) define'.*include/php' $(top_srcdir)/configure | $(SED) 's/.*>//'|xargs rm -f

.PHONY: all clean install distclean test
.NOEXPORT:
lib4d_sql/base64.lo: /home/edv-administrator/pdo4d/lib4d_sql/base64.c
	$(LIBTOOL) --mode=compile $(CC) -I/usr/include/php/ext -I/home/edv-administrator/pdo4d/lib4d_sql -I. -I/home/edv-administrator/pdo4d $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/edv-administrator/pdo4d/lib4d_sql/base64.c -o lib4d_sql/base64.lo 
lib4d_sql/communication.lo: /home/edv-administrator/pdo4d/lib4d_sql/communication.c
	$(LIBTOOL) --mode=compile $(CC) -I/usr/include/php/ext -I/home/edv-administrator/pdo4d/lib4d_sql -I. -I/home/edv-administrator/pdo4d $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/edv-administrator/pdo4d/lib4d_sql/communication.c -o lib4d_sql/communication.lo 
lib4d_sql/fourd.lo: /home/edv-administrator/pdo4d/lib4d_sql/fourd.c
	$(LIBTOOL) --mode=compile $(CC) -I/usr/include/php/ext -I/home/edv-administrator/pdo4d/lib4d_sql -I. -I/home/edv-administrator/pdo4d $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/edv-administrator/pdo4d/lib4d_sql/fourd.c -o lib4d_sql/fourd.lo 
lib4d_sql/fourd_interne.lo: /home/edv-administrator/pdo4d/lib4d_sql/fourd_interne.c
	$(LIBTOOL) --mode=compile $(CC) -I/usr/include/php/ext -I/home/edv-administrator/pdo4d/lib4d_sql -I. -I/home/edv-administrator/pdo4d $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/edv-administrator/pdo4d/lib4d_sql/fourd_interne.c -o lib4d_sql/fourd_interne.lo 
lib4d_sql/fourd_result.lo: /home/edv-administrator/pdo4d/lib4d_sql/fourd_result.c
	$(LIBTOOL) --mode=compile $(CC) -I/usr/include/php/ext -I/home/edv-administrator/pdo4d/lib4d_sql -I. -I/home/edv-administrator/pdo4d $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/edv-administrator/pdo4d/lib4d_sql/fourd_result.c -o lib4d_sql/fourd_result.lo 
lib4d_sql/fourd_type.lo: /home/edv-administrator/pdo4d/lib4d_sql/fourd_type.c
	$(LIBTOOL) --mode=compile $(CC) -I/usr/include/php/ext -I/home/edv-administrator/pdo4d/lib4d_sql -I. -I/home/edv-administrator/pdo4d $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/edv-administrator/pdo4d/lib4d_sql/fourd_type.c -o lib4d_sql/fourd_type.lo 
lib4d_sql/sqlstate.lo: /home/edv-administrator/pdo4d/lib4d_sql/sqlstate.c
	$(LIBTOOL) --mode=compile $(CC) -I/usr/include/php/ext -I/home/edv-administrator/pdo4d/lib4d_sql -I. -I/home/edv-administrator/pdo4d $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/edv-administrator/pdo4d/lib4d_sql/sqlstate.c -o lib4d_sql/sqlstate.lo 
lib4d_sql/utils.lo: /home/edv-administrator/pdo4d/lib4d_sql/utils.c
	$(LIBTOOL) --mode=compile $(CC) -I/usr/include/php/ext -I/home/edv-administrator/pdo4d/lib4d_sql -I. -I/home/edv-administrator/pdo4d $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/edv-administrator/pdo4d/lib4d_sql/utils.c -o lib4d_sql/utils.lo 
pdo_4d.lo: /home/edv-administrator/pdo4d/pdo_4d.c
	$(LIBTOOL) --mode=compile $(CC) -I/usr/include/php/ext -I/home/edv-administrator/pdo4d/lib4d_sql -I. -I/home/edv-administrator/pdo4d $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/edv-administrator/pdo4d/pdo_4d.c -o pdo_4d.lo 
4d_driver.lo: /home/edv-administrator/pdo4d/4d_driver.c
	$(LIBTOOL) --mode=compile $(CC) -I/usr/include/php/ext -I/home/edv-administrator/pdo4d/lib4d_sql -I. -I/home/edv-administrator/pdo4d $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/edv-administrator/pdo4d/4d_driver.c -o 4d_driver.lo 
4d_statement.lo: /home/edv-administrator/pdo4d/4d_statement.c
	$(LIBTOOL) --mode=compile $(CC) -I/usr/include/php/ext -I/home/edv-administrator/pdo4d/lib4d_sql -I. -I/home/edv-administrator/pdo4d $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/edv-administrator/pdo4d/4d_statement.c -o 4d_statement.lo 
$(phplibdir)/pdo_4d.la: ./pdo_4d.la
	$(LIBTOOL) --mode=install cp ./pdo_4d.la $(phplibdir)

./pdo_4d.la: $(shared_objects_pdo_4d) $(PDO_4D_SHARED_DEPENDENCIES)
	$(LIBTOOL) --mode=link $(CC) $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS) $(LDFLAGS) -o $@ -export-dynamic -avoid-version -prefer-pic -module -rpath $(phplibdir) $(EXTRA_LDFLAGS) $(shared_objects_pdo_4d) $(PDO_4D_SHARED_LIBADD)

