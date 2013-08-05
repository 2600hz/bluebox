import json
import os

# Logger creation
import logging
log = logging.getLogger(__name__)

from django.conf import settings
from django.http import HttpResponse
from django.views.decorators.csrf import csrf_exempt

from lxml import etree
from bluebox.dialplan.extension.models import Extension

@csrf_exempt
def create(request, account_id):
    if request.method == 'PUT':
        json_obj = json.loads(request.body)

        extension = Extension()
        if extension.create(account_id, json_obj):
            return HttpResponse('OK')
        else:
            return HttpResponse('Nahhhh, it did not create the extension')

    return HttpResponse('Not a PUT')

def list_extension(request, account_id):
    lists = os.listdir("%s/%s/directory/" % (settings.BLUEBOX_CONFIG_PATH, account_id))
    list_array = {'data':{'accounts':{}}}

    for file_name in lists:
        if not os.path.isdir("%s/%s/directory/%s" % (settings.BLUEBOX_CONFIG_PATH, account_id, file_name)):
            tree = etree.parse("%s/%s/directory/%s" % (settings.BLUEBOX_CONFIG_PATH, account_id, file_name))

            extension = tree.find('//extension')
            extension_name = extension.get('name')

            element_dict = { "name":extension_name}

            list_array['data']['accounts'][file_name[:-4]] = element_dict

    return HttpResponse(json.dumps(list_array, indent=4), content_type='application/json')