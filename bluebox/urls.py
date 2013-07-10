from django.conf.urls import patterns, include, url

urlpatterns = patterns('',
    url(r'^account/(?P<account_id>[a-z0-9]{32})/directory/', include('bluebox.directory.urls', namespace='directory'))
)
