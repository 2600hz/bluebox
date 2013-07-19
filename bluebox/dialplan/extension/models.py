import uuid

from lxml import etree

from django.conf import settings

class Extension:
    def _generate_xml(self, data):
        root = etree.Element('include')

        extension = etree.Element('extension', name=data['name'], bb_dialplan_module='extensions')
        root.append(extension)

        condition = etree.Element('condition', field='destination_number', expression='^%s$' % data['extension'])
        extension.append(condition)

        condition.append(etree.Element('action', application='set', data='ringback=${us-ring}'))
        condition.append(etree.Element('action', application='set', data='transfer_ringback=$${hold_music}'))
        condition.append(etree.Element('action', application='set', data='call_timeout=30'))
        condition.append(etree.Element('action', application='set', data='hangup_after_bridge=true'))
        condition.append(etree.Element('action', application='set', data='continue_on_fail=true'))
        condition.append(etree.Element('action', application='bridge', data='user/%s@${domain_name}' % data['username']))
        condition.append(etree.Element('action', application='answer'))
        condition.append(etree.Element('action', application='sleep', data='1000'))
        condition.append(etree.Element('action', application='voicemail', data='default ${domain} %s' % data['username']))

        return root

    def create(self, account_id, data):
        # The file name
        uuid_str = str(uuid.uuid1())
        target_file = open("%s%s/dialplan/%s.xml" % (settings.BLUEBOX_CONFIG_PATH, account_id, uuid_str), 'w+')

        etree_obj = self._generate_xml(data)
        string_xml = etree.tostring(etree_obj, pretty_print=True)

        target_file.write(string_xml)

        return True