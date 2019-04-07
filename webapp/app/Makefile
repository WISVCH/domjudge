ifndef TOPDIR
TOPDIR=../..
endif
include $(TOPDIR)/Makefile.global

TARGETS =
OBJECTS =

PARAMS = config/parameters.yml
STATIC = config/static.yml

SUBST_FILES = autoload.php $(STATIC)

$(SUBST_FILES): %: %.in $(TOPDIR)/paths.mk
	$(substconfigvars)

build: $(TARGETS) $(SUBST_FILES)

domserver: $(SUBST_FILES)

# Don't try to create dbpasswords.secret here if it doesn't exist:
$(TOPDIR)/etc/dbpasswords.secret:

$(PARAMS): %: %.dist $(TOPDIR)/etc/dbpasswords.secret
	@[ -n "$(QUIET)" ] || echo "Generating '$@'..."
	$(TOPDIR)/etc/gensymfonyparams $^ > $@

# Rule to generate $(PARAMS) file from a stub during 'make dist':
params-from-stub: $(PARAMS).stub
	if [ ! -r $(PARAMS) ]; then \
		cp $< $(PARAMS) ; \
		touch -d '1971-01-01 00:00:00' $(PARAMS) ; \
	fi

# Rule to generate $(STATIC) file from a stub during 'make dist':
static-from-stub: $(STATIC).stub
	if [ ! -r $(STATIC) ]; then \
		cp $< $(STATIC) ; \
		touch -d '1971-01-01 00:00:00' $(STATIC) ; \
	fi

clean-l:
	-rm -f $(TARGETS) $(OBJECTS)

distclean-l:
	-rm -f $(SUBST_FILES)

.PHONY: params-from-stub static-from-stub