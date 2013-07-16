import json

# Logger creation
import logging
log = logging.getLogger(__name__)

from django.http import HttpResponse
from django.views.decorators.csrf import csrf_exempt

from bluebox.dialplan.extensions.models import Extension

@csrf_exempt
def create(request, account_id):
    extension = Extension()

    if request.method == 'PUT':
        json_obj = json.loads(request.body)

        if extension.create(account_id, json_obj):
            return HttpResponse('OK')
        else:
            return HttpResponse('Nahhhh, it did not create the extension')

    return HttpResponse('Not a PUT')
