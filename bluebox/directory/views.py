import json

from django.http import HttpResponse
from django.views.decorators.csrf import csrf_exempt

# Logger creation
import logging
log = logging.getLogger(__name__)

from bluebox.directory.models import Directory

@csrf_exempt
def create(request, account_id):
    # Creating directory object
    directory = Directory()

    if request.method == 'PUT':
        json_obj = json.loads(request.body)

        directory.create_user(account_id, json_obj)
        return HttpResponse()

    return HttpResponse('Not a PUT')

def delete(request, account_id):
    pass

def edit(request, account_id):
    pass