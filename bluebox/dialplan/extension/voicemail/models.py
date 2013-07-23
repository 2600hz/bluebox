from django.db import models
import os
from lxml import etree
import logging
log = logging.getLogger(__name__)

from django.conf import settings

from bluebox.helpers import Utils

class Voicemail:

    def _generate_xml(self,data,tree):
        application = tree.find("//condition")
        application.append(etree.Element('action', application="answer"))
        application.append(etree.Element('action', application="sleep", data="1000"))
        application.append(etree.Element('action', application="voicemail", data="default ${domain} %s" % data['voicemail_number']))
        return tree

    def add(self, account_id, file_id, data):
        parser = etree.XMLParser(remove_blank_text=True)
        tree = etree.parse("%s/%s/dialplan/%s.xml" % (settings.BLUEBOX_CONFIG_PATH, account_id, file_id), parser)
        tree = self._generate_xml(data, tree)
        tree.write("%s/%s/dialplan/%s.xml" % (settings.BLUEBOX_CONFIG_PATH, account_id, file_id), pretty_print=True)
        return True