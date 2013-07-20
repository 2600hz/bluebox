from django.db import models
import os
from lxml import etree
import logging
log = logging.getLogger(__name__)

from django.conf import settings

from bluebox.helpers import Utils

class Voicemail:
    def _modify_xml(self, data, tree):
        params = tree.find('//params')
        params.append(etree.Element('param', name='vm-password', value=data['password']))
        return tree
    
   
    def delete(self, user_id):
        os.remove("%s/%s/directory/%s.xml" % (settings.BLUEBOX_CONFIG_PATH, account_id, user_id))


    def create(self, account_id, user_id, data):
        parser = etree.XMLParser(remove_blank_text=True)
        tree = etree.parse("%s%s/directory/%s.xml" % (settings.BLUEBOX_CONFIG_PATH, account_id, user_id), parser)
        tree = self._modify_xml(data, tree)
        tree.write("%s%s/directory/%s.xml" % (settings.BLUEBOX_CONFIG_PATH, account_id, user_id), pretty_print=True)