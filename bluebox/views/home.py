import json

from django.http import HttpResponse
from django.views.decorators.csrf import csrf_exempt

# Logger creation
import logging
log = logging.getLogger(__name__)

from bluebox.models import Directory

@csrf_exempt
def home(request):
    # Creating directory object
    directory = Directory()

    if request.method == 'PUT':
        # raw_post_data represent the received data
        # and yes, it is post even though we are in a PUT request
        json_obj = json.loads(request.raw_post_data)

        directory.create_user(json_obj)
        return HttpResponse("Ok creation")

    return HttpResponse("Fail")