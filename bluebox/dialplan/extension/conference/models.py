import uuid

from django.conf import settings

from lxml import etree


class Conference:
    def _generate_xml(self, data):
        root = etree.Element('include')

        extension = etree.Element('extension', name=data['name'], bb_dialplan_module='extension_conference')
        root.append(extension)

        condition = etree.Element('condition', field='destination_number', expression='^%s$' % data['destination_number'])
        extension.append(condition)

        condition.append(etree.Element('action', application='answer'))    
        condition.append(etree.Element('action', application='sleep', data='500'))
        condition.append(etree.Element('action', application='conference', data='%s@default' % data['name']))

        return root

    def create(self, account_id, data):
        # The file name
        uuid_str = str(uuid.uuid1())
        target_file = open("%s%s/dialplan/%s.xml" % (settings.BLUEBOX_CONFIG_PATH, account_id, uuid_str), 'w+')

        etree_obj = self._generate_xml(data)
        string_xml = etree.tostring(etree_obj, pretty_print=True)

        target_file.write(string_xml)

        return True