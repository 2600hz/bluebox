from django.conf.urls import patterns, include, url
import views

urlpatterns = patterns('',
    url(r'^account/(?P<account_id>[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})/directory/', include('bluebox.directory.urls', namespace='directory')),
    url(r'^account/(?P<account_id>[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})/dialplan/', include('bluebox.dialplan.urls', namespace='dialplan')),
    url(r'^account/(?P<account_id>[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})/configuration/', include('bluebox.configuration.urls', namespace='configuration')),
    url(r'^account/list/$', views.list, name='list')
)