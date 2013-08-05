from django.conf.urls import patterns, include, url
from .views import *

urlpatterns = patterns('',
    url(r'^create/', create, name='create'),
    url(r'^list/', list_profiles, name='list_profiles')
    #url(r'^edit/', edit, name='edit'),
    #url(r'^delete/', delete, name='delete')

)