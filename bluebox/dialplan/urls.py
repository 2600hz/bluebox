from django.conf.urls import patterns, inclue, urls

from bluebox.dialplan import views

urlpatterns = patterns('',
	url(r'^create/$', views.create, name='create'),
    url(r'^delete/(?P<user_id>[a-z0-9]{1,}$)', views.delete, name='delete'),
    url(r'^edit/$', views.edit, name='edit')
)