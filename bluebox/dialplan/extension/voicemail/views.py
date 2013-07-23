import json
import os

# Logger creation
import logging
log = logging.getLogger(__name__)

from django.conf import settings
from django.http import HttpResponse
from django.views.decorators.csrf import csrf_exempt
from bluebox.dialplan.extension.voicemail.models import Voicemail

@csrf_exempt
def add(request, account_id, file_id):
    if request.method == 'PUT':
        json_obj = json.loads(request.body)

        voicemail = Voicemail()
        if voicemail.add(account_id, file_id, json_obj):
            return HttpResponse('OOOOOK')
        else:
            return HttpResponse('NOPE')

    return HttpResponse('Not a put')