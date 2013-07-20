from django.conf.urls import patterns, include, url

from bluebox.directory.voicemail import views

urlpatterns = patterns('',
	url(r'^create/$', views.create, name='create')
)