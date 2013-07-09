import json

from django.http import HttpResponse
from django.views.decorators.csrf import csrf_exempt

from bluebox.models import Directory

@csrf_exempt
def home(request):
    # Creating directory object
    directory = Directory()

    if request.method == 'PUT':
        xml = directory.generate_xml(request)
        directory.create_user(xml)
        return HttpResponse("Ok creation")

    return HttpResponse("Fail")