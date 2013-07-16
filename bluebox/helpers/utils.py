class Utils:
    @staticmethod
    def get_target_file_path(account_id, module, filename):
        return '%s%s/%s/%s.xml' % (settings.BLUEBOX_CONFIG_PATH, account_id, module, filename)
