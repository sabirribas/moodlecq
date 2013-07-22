#from django.conf.urls import patterns, include, url

from django.conf.urls.defaults import *

from django.http import HttpResponse

# Uncomment the next two lines to enable the admin:
# from django.contrib import admin
# admin.autodiscover()

import settings

urlpatterns = patterns('',
    # Examples:
    # url(r'^$', 'srlab.views.home', name='home'),
    # url(r'^srlab/', include('srlab.foo.urls')),

    # Uncomment the admin/doc line below to enable admin documentation:
    # url(r'^admin/doc/', include('django.contrib.admindocs.urls')),

    # Uncomment the next line to enable the admin:
    # url(r'^admin/', include(admin.site.urls)),

    url(r'^$', lambda r: HttpResponse(".", mimetype="text/html")),

    url(r'^quap/', include('quap.urls')),
    
    url(r'^static/(?P<path>.*)$', 'django.views.static.serve',
         {'document_root':     settings.STATIC_ROOT}),

    url(r'^media/(?P<path>.*)$', 'django.views.static.serve',
         {'document_root':     settings.MEDIA_ROOT}),

)
