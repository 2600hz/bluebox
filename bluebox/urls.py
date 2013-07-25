from django.conf.urls import patterns, include, url
import views

urlpatterns = patterns('',
    url(r'^account/(?P<account_id>[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})/directory/', include('bluebox.directory.urls', namespace='directory')),
    url(r'^account/(?P<account_id>[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})/dialplan/', include('bluebox.dialplan.urls', namespace='dialplan')),
    url(r'^account/(?P<account_id>[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})/autoload_configs/', include('bluebox.autoload_configs.urls', namespace='configuration')),
	url(r'^account/list/$', views.list, name='list'),
    url(r'^account/(?P<account_id>[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})/dialplan/extension/', include('bluebox.dialplan.extension.urls', namespace='extension'))
)