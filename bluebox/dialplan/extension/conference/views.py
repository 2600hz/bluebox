import json

from django.http import HttpResponse
from django.views.decorators.csrf import csrf_exempt
from django.conf import settings

from .models import Conference

@csrf_exempt
def create(request, account_id):
    if request.method == 'PUT':
        json_obj = json.loads(request.body)

        conference = Conference()
        if conference.create(account_id, json_obj):
            return HttpResponse('OK')
        else:
            return HttpResponse('Nahhhh, it did not create the extension')

    return HttpResponse('Not a PUT')