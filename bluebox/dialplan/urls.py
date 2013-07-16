from django.conf.urls import patterns, include, urls

urlpatterns = patterns('',
	url(r'^extension/', include('bluebox.dialplan.extension.urls', namespace='extension'))
)