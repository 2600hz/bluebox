import json
import os

# Logger creation
import logging
log = logging.getLogger(__name__)

from django.conf import settings
from django.http import HttpResponse
from django.views.decorators.csrf import csrf_exempt

from lxml import etree
#from bluebox.autoload_configs.conference.models import Conference

def add(request):
    pass

def list_config(request, account_id):
    list_profiles = {}
    parser = etree.XMLParser(remove_comments=True)
    tree = etree.parse("%s/%s/autoload_configs/conference_conf.xml" % (settings.BLUEBOX_CONFIG_PATH, account_id), parser)
    profiles = tree.find('//profiles')

    #element_dict = { "value":profile_value}

    for profile in profiles:
        profile_name = profile.get("name")
        list_profiles[profile_name] = {}
        for param in profile:
            list_profiles[profile_name][param.get("name")] = param.get("value")
    
    return HttpResponse(json.dumps(list_profiles, indent=4), content_type='application/json')

def edit(request):
    pass

def delete(request):
    pass

