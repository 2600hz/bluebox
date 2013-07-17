import json
import os
# Logger creation
import logging
log = logging.getLogger(__name__)

from django.http import HttpResponse
from django.views.decorators.csrf import csrf_exempt
from django.conf import settings

from bluebox.directory.models import Directory

@csrf_exempt
def create(request, account_id):
    # Creating directory object
    directory = Directory()

    if request.method == 'PUT':
        # raw_post_data represent the received data
        # and yes, it is post even though we are in a PUT request
        json_obj = json.loads(request.body)

        directory.create(account_id, json_obj)
        return HttpResponse()

    return HttpResponse('Not a PUT')

def delete(request, account_id, user_id):
    directory = Directory()

    if request.method == 'DELETE':
        directory.delete(account_id, user_id)

        return HttpResponse('DELETE should be done')

    return HttpResponse('Not a DELETE')

def edit(request, account_id):
    directory = Directory()

    if request.method == 'POST':
        return HttpResponse('POST ok')

    return HttpResponse('Not a POST')

def list(request, account_id):
    directory = Directory()
    lists = os.listdir("%s/%s/directory/" % (settings.BLUEBOX_CONFIG_PATH, account_id))
    list_array = {'data':{'accounts':[]}}

    for file_name in lists:
        if os.path.isdir("%s/%s/directory/%s" % (settings.BLUEBOX_CONFIG_PATH, account_id, file_name)) == False:
            list_array['data']['accounts'].append(file_name[:-4])
            # if os.path.isdir("%s/%s" % (settings.BLUEBOX_CONFIG_PATH, file_name)) == True:

    return HttpResponse(json.dumps(list_array, indent=4), content_type='application/json')