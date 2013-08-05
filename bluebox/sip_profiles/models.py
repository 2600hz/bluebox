import json
import os
import logging
log = logging.getLogger(__name__)

from lxml import etree

from django.conf import settings

from bluebox.helpers import Utils


class Profiles:
    def add(request, account_id):
        parser = etree.XMLParser(remove_blank_text=True)
        tree = etree.parse("%s/%s/sip_profiles/%s/%s" % (settings.BLUEBOX_CONFIG_PATH, account_id, data[type]))




    def list_profiles(request, account_id):
        lists = os.listdir("%s/%s/sip_profiles/" % (settings.BLUEBOX_CONFIG_PATH, account_id))
        list_array = {'data':{}}
        for target in lists:
            if os.path.isdir("%s/%s/sip_profiles/%s" % (settings.BLUEBOX_CONFIG_PATH, account_id, target)):
                file_names = os.listdir("%s/%s/sip_profiles/%s/" % (settings.BLUEBOX_CONFIG_PATH, account_id, target))
                for file_name in file_names:
                    parser = etree.XMLParser(remove_comments=True) 
                    tree = etree.parse("%s/%s/sip_profiles/%s/%s" % (settings.BLUEBOX_CONFIG_PATH, account_id, target, file_name), parser)
                    params = tree.find('//gateway')
                    list_array['data'][target] = {}
                    for param in params:
                        log.debug(param.get('name'))
                        #list_array['data'][target][file_name] = {}
                        list_array['data'][target][param.get('name')] = param.get('value')
        return list_array

    def _generate_xml(self, json_obj):
        root = etree.Element('include')
        for gateway_name in json_obj['data']:
            gateway = etree.Element('gateway', name=data['name'])
            #root.append(gateway)
            #for name, value in json_obj['data'][params]:
                #params.append(etree.Element('param', name=name, value=))
        return tree

    def create(self, account_id, data):
        uuid_str = str(uuid.uuid1())
        log.debug(uuid_str)
        target_file = open("settings.BLUEBOX_CONFIG_PATH/%s/%s/sip_profiles"(account_id, uuid_str), 'w')
        log.debug(target_file)
        etree_obj = self._generate_xml(data)
        string_xml= etree.tostring(etree_obj, pretty_print=True)
        target_file.write(string_xml)

        return True
