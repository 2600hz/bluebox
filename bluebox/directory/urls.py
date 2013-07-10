from django.conf.urls import patterns, include, url

from bluebox.directory import views

urlpatterns = patterns('',
    url(r'^create/$', views.home, name='home')
)