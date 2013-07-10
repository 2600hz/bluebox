from django.conf.urls import patterns, include, url

from bluebox.directory import views

urlpatterns = patterns('',
    url(r'^create/$', views.create, name='create'),
    url(r'^delete/$', views.delete, name='delete'),
    url(r'^edit/$', views.edit, name='edit')
)