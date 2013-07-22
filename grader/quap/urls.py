#from django.conf.urls import patterns, include, url

from django.conf.urls.defaults import *

urlpatterns = patterns('quap.views',
	url(r'^test[/]$', 'test'),
	url(r'^test_socket[/]$', 'test_socket'),
	url(r'^testcode[/]$', 'testcode'),
)
