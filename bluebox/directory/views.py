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
        # raw_post_data represent the received data
        # and yes, it is post even though we are in a PUT request
        json_obj = json.loads(request.body)

        directory.create_user(account_id, json_obj)
        return HttpResponse()

    return HttpResponse('Not a PUT')

def delete(request, account_id):
    directory = Directory()

    if request.method == 'DELETE':
        json_obj = json.loads(request.body)
        directory.delete_user(account_id, json_obj['user_id'])

        return HttpResponse('DELETE should be done')

    return HttpResponse('Not a DELETE')

def edit(request, account_id):
    directory = Directory()

    if request.method == 'POST':
        return HttpResponse('POST ok')

    return HttpResponse('Not a POST')