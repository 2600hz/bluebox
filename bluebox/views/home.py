import json

from django.http import HttpResponse

from bluebox.models import Directory

def home(request):
    directory = Directory()
    directory.create()
    return HttpResponse("hello world")