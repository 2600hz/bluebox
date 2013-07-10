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

    log.debug(account_id)

    if request.method == 'PUT':
        # raw_post_data represent the received data
        # and yes, it is post even though we are in a PUT request
        json_obj = json.loads(request.raw_post_data)

        directory.create_user(json_obj)
        return HttpResponse()

    return HttpResponse('Not a PUT')

def delete(request, account_id):
    pass

def edit(request, account_id):
    pass