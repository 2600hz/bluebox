from django.conf.urls import patterns, include, url
from .views import *

urlpatterns = patterns('',
    url(r'^add/', add, name='add'),
    url(r'^list/', list_config, name='list_config'),
    url(r'^edit/', edit, name='edit'),
    url(r'^delete/', delete, name='delete')
)