import json
import os

# Logger creation
import logging
log = logging.getLogger(__name__)

from django.conf import settings
from django.http import HttpResponse
from django.views.decorators.csrf import csrf_exempt

from lxml import etree

from bluebox.autoload_configs.conference.models import Conference

@csrf_exempt
def add(request, account_id):
    if request.method == 'PUT':
        json_obj = json.loads(request.body)

        conference = Conference()
        if conference.add(account_id, json_obj):
            return HttpResponse('ok')
        else:
            return HttpResponse('Nope')
    return HttpResponse ('Not a put')


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

@csrf_exempt
def edit(request, account_id):
    if request.method == 'POST':
        json_obj = json.loads(request.body)

        conference = Conference()
        if conference.edit(account_id, json_obj):
            return HttpResponse('ok')
        else:
            return HttpResponse('Nope')
    return HttpResponse ('Not a POST')


@csrf_exempt
def delete(request, account_id):
    if request.method == 'DELETE':
        json_obj = json.loads(request.body)

        conference = Conference()
        if conference.delete(account_id, json_obj):
            return HttpResponse('ok')
        else:
            return HttpResponse('Nope')
    return HttpResponse ('Not a delete')

