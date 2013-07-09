from lxml import etree

class Directory:
    def create_user(self, etree_obj):
        # Opening the file
        target_file = open('test.xml', 'w')

        # XML to string
        string_xml = etree.tostring(etree_obj, pretty_print=True)

        target_file.write(string_xml)

    def generate_xml(self, request):
        # The root element here must be include since this file will be included
        # In the directory file
        root = etree.Element('include')

        # -- user --
        user = etree.Element('user', id='2000')
        root.append(user)
        # --- params ---
        params = etree.Element('params')
        user.append(params)
        # ---- param ----
        params.append(etree.Element('param', name='password', value='$${default_password}'))
        params.append(etree.Element('param', name='vm-password', value='2000'))

        return root