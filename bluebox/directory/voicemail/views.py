from django.http import HttpResponse
import logging
import json
log = logging.getLogger(__name__)
from django.views.decorators.csrf import csrf_exempt
from django.conf import settings

from lxml import etree

from bluebox.directory.voicemail.models import Voicemail

@csrf_exempt
def create(request, account_id, user_id):
    voicemail = Voicemail()

    if request.method == 'PUT':
        json_obj = json.loads(request.body)
        voicemail.create(account_id, user_id, json_obj)
        return HttpResponse()

    return HttpResponse('Bim Bim Bim ! ')


