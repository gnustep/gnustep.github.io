from sphinxcontrib.domaintools import custom_domain

def setup(app):
    app.add_domain(custom_domain('GnuMakeDomain',
        name  = 'make',
        label = "GNU Make",

        elements = dict(
            target = dict(
                objname      = "Make Target",
                indextemplate = "target %s",
            ),
            var   = dict(
                objname = "Make Variable",
                indextemplate = "pair %s"
            ),
        )))

    return {
        'version': '0.1',
    }