import os
import ESL

from lxml import etree

from django.conf import settings

from bluebox.helpers import Utils

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

        esl_con = ESL.ESLconnection("127.0.0.1", "8021", "ClueCon")
        esl_con.api('reloadxml')

        return root

    def create_user(self, account_id, data):
        # Opening the file
        target_file = open(Utils.get_target_file_path(account_id, 'directory', data['user_id']), 'w')

        # Creating etree_obj
        etree_obj = self._generate_xml(data)
        # XML to string
        string_xml = etree.tostring(etree_obj, pretty_print=True)

        # Finally writing file to disk
        target_file.write(string_xml)

        return True

    def delete_user(self, account_id, user_id):
        # simply deleting the file
        os.remove(Utils.get_target_file_path(account_id, 'directory', user_id))