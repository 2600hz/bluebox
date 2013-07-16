from django.conf.urls import patterns, include, urls

urlpatterns = patterns('',
	url(r'^extensions/', include('bluebox.dialplan.extensions.urls', namespace='extensions'))
)