import json
import os
# Logger creation
import logging
log = logging.getLogger(__name__)

from django.http import HttpResponse
from django.views.decorators.csrf import csrf_exempt
from django.conf import settings

from bluebox.directory.models import Directory
from lxml import etree


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
    lists = os.listdir("%s/%s/directory/" % (settings.BLUEBOX_CONFIG_PATH, account_id))
    list_array = {'data':{'accounts':[]}}

    for file_name in lists:
        if os.path.isdir("%s/%s/directory/%s" % (settings.BLUEBOX_CONFIG_PATH, account_id, file_name)) == False:
            tree = etree.parse("%s/%s/directory/%s" % (settings.BLUEBOX_CONFIG_PATH, account_id, file_name))

            user = tree.find('//user')
            user_id = user.get('id')

            element_dict = {file_name[:-4]:{ "id":user_id}}
            
            #list_array['data']['accounts'].append(element_dict)
            list_array['data']['accounts'].extend([element_dict])
            #plop = list_array['data']['accounts']
            #print(plop.extend([element_dict]))
            #print plop['data']
            #element_dict = {file_name[:-4], "id":user_id}
            #dataaa = json.loads(plop)
            #print dataaa['data']
            #list_array['data']['accounts'].append(file_name[:-4])
    return HttpResponse(json.dumps(list_array, indent=4), content_type='application/json')