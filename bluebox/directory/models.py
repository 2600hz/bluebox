from lxml import etree

from django.conf import settings

class Directory:
    def _generate_xml(self, data):
        # The root element here must be include since this file will be included
        # In the directory file
        root = etree.Element('include')

        # -- user --
        user = etree.Element('user', id=data['user_id'])
        root.append(user)
        # --- params ---
        params = etree.Element('params')
        user.append(params)
        # ---- param ----
        params.append(etree.Element('param', name='password', value=data['user_password']))
        params.append(etree.Element('param', name='vm-password', value=data['user_vm_password']))

        return root

    def create_user(self, account_id, data):
        # Opening the file
        target_file = open('%s%s/directory/%s.xml' % (settings.BLUEBLOX_CONFIG_PATH, account_id, data['user_id']), 'w')

        # Creating etree_obj
        etree_obj = self._generate_xml(data)
        # XML to string
        string_xml = etree.tostring(etree_obj, pretty_print=True)

        # Finally writing file to disk
        target_file.write(string_xml)