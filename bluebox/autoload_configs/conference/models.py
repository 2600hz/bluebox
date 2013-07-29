import os
import logging
log = logging.getLogger(__name__)

from lxml import etree

from django.db import models
from django.conf import settings

from bluebox.helpers import Utils

class Conference:
    def add(self, account_id, data):
        parser = etree.XMLParser(remove_blank_text=True)
        tree = etree.parse("%s/%s/autoload_configs/conference_conf.xml" % (settings.BLUEBOX_CONFIG_PATH, account_id), parser)
        tree = self._generate_xml(data, tree)
        tree.write("%s/%s/autoload_configs/conference_conf.xml" % (settings.BLUEBOX_CONFIG_PATH, account_id), pretty_print=True)
        return True

    def _generate_xml(self, data, tree):
        for profile in data:
            profile_target = tree.find('//profiles/profile[@name="%s"]' % profile)
            for param in data[profile]:
                profile_target.append(etree.Element("param", name=param['name'], value=param['value']))
        
        return tree

    def delete(self, account_id, data ):
        parser = etree.XMLParser(remove_blank_text=True)
        tree = etree.parse("%s/%s/autoload_configs/conference_conf.xml" % (settings.BLUEBOX_CONFIG_PATH, account_id), parser)
        for profile in data:
            for param in data[profile]:
                profile_target = tree.find('//profiles/profile[@name="%s"]' % profile)
                profile_target.remove(profile_target.find('param[@name="%s"]' % param))
        tree.write("%s/%s/autoload_configs/conference_conf.xml" % (settings.BLUEBOX_CONFIG_PATH, account_id), pretty_print=True)
        return True

    def edit(self, account_id, data):
        parser = etree.XMLParser(remove_blank_text=True)
        tree = etree.parse("%s/%s/autoload_configs/conference_conf.xml" % (settings.BLUEBOX_CONFIG_PATH, account_id), parser)
        for profile in data:
            for param in data[profile]:
                for row_target in param:
                    row = tree.find('//profiles/profile[@name="%s"]/param[@name="%s"]' % (profile, row_target))
                    row.set("value", param[row_target])
        tree.write("%s/%s/autoload_configs/conference_conf.xml" % (settings.BLUEBOX_CONFIG_PATH, account_id), pretty_print=True)


