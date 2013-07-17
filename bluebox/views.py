import os
import json
import logging
log = logging.getLogger(__name__)

from django.http import HttpResponse
from django.conf import settings

def list (request):
    lists = os.listdir(settings.BLUEBOX_CONFIG_PATH)
    list_array = {'data':{'accounts':[]}}
    for file_name in lists:
        if os.path.isdir("%s/%s" % (settings.BLUEBOX_CONFIG_PATH, file_name)) == True:
            list_array['data']['accounts'].append(file_name)

    return HttpResponse(json.dumps(list_array, indent=4), content_type='application/json')