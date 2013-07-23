from django.conf.urls import patterns, include, url

urlpatterns = patterns('',
    url(r'^conference/', include('bluebox.autoload_configs.conference.urls', namespace='autoload_configs'))
)