from django.conf.urls import patterns, include, url

urlpatterns = patterns('',
    url(r'^conference/', include('bluebox.configuration.conference.urls', namespace='conference'))
)