import json
import os

# Logger creation
import logging
log = logging.getLogger(__name__)

from django.conf import settings
from django.http import HttpResponse
from django.views.decorators.csrf import csrf_exempt

from lxml import etree

from bluebox.sip_profiles.models import Profiles

@csrf_exempt
def add(request, account_id):
    if request.method == 'PUT':
        json_obj = json.loads(request.body)

        profile = Profiles()
        if profile.add(account_id, json_obj):
            return HttpResponse('ok')
        else:
            return HttpResponse('Nope')
    return HttpResponse ('Not a put')

@csrf_exempt
def list_profiles(request, account_id):
    if request.method == 'GET':

        profile = Profiles()
        answer = profile.list_profiles(account_id)
        if answer:
            return HttpResponse(json.dumps(answer, indent=4), content_type='application/json')
        else:
            return HttpResponse('Nope')
        return HttpResponse('Not a Get')

def delete(request, account_id):
    return True

@csrf_exempt
def create(request, account_id):
    profile = Profiles()

    if request.method == 'PUT':
        json_obj = json.loads(request.body)

        profile.create(account_id, json_obj)
        return HttpResponse()

    return HttpResponse('Not a Put')