from lxml import etree

class Directory:
    def create_user(self):
        # Opening the file
        target_file = open('test.xml', 'w')

        # XML content
        root = etree.Element("root")
        root.set("interesting", "somewhat")
        child1 = etree.SubElement(root, "test")
        # To string
        string_xml = etree.tostring(root, pretty_print=True)

        target_file.write(string_xml)