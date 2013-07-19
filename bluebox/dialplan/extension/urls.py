from django.conf.urls import patterns, include, url

from bluebox.dialplan.extensions import views

urlpatterns = patterns('',
    url(r'^create/$', views.create, name='create')
)