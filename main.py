#!/usr/bin/python3 -O

from jnpr.junos import Device
from jnpr.junos.exception import ConnectError
from lxml import etree
from netaddr import *
import datetime
import re
import os

USERNAME = 'your_username_here'
PASSWORD = 'VerY_SecrEt_PASSw0rd'

CONFIG_DIR = 'data/set_conf'
STAT_DIR = 'data/stat'

now = datetime.datetime.now()

# Remove All config files first
filelist = [ f for f in os.listdir(CONFIG_DIR) if f.endswith(".conf") ]
for f in filelist:
    os.remove(os.path.join(CONFIG_DIR, f))

# Remove All statistics
filelist = [ f for f in os.listdir(STAT_DIR) if f.endswith(".xml") ]
for f in filelist:
    os.remove(os.path.join(STAT_DIR, f))


def add_impossible(HOSTNAME):
    hostname_conf = 'data/set_conf/' +HOSTNAME+ '.conf'
    f_config = open(hostname_conf, "w")
    f_config.write(HOSTNAME + " - Impossible to add pool (All BNGMX devices are Full !!!)\n")
    f_config.close()


def add_config(link,net,HOSTNAME, n_sid):
    hostname_conf = 'data/set_conf/' +HOSTNAME+ '.conf'
    f_config = open(hostname_conf, "w")

    f_run = open("data/const/run.txt", "w")
    f_run.write ("Making config for " +HOSTNAME+ "...\n")
    f_run.close()


    link_name = re.split('_POOL_', link.strip())
    n_pool = (link_name[0].strip()+ "_POOL_" +str(int(link_name[1].strip()) + 1))
    n_lo = str(IPNetwork(net).network+1)
    n_low = str(IPNetwork(net).network+2)
    n_high = str(IPNetwork(net).broadcast-1)

    f_config.write("set access address-assignment pool " +link+ " link " +n_pool+ "\n")
    f_config.write("set access address-assignment pool " +n_pool+ "  family inet network " +net+ "\n")
    f_config.write("set access address-assignment pool " +n_pool+ "  family inet range Range1 low " +n_low+ "\n")
    f_config.write("set access address-assignment pool " +n_pool+ "  family inet range Range1 high " +n_high+ "\n")
    f_config.write("set access address-assignment pool " +n_pool+ "  family inet dhcp-attributes maximum-lease-time 600\n")
    f_config.write("set access address-assignment pool " +n_pool+ "  family inet dhcp-attributes server-identifier " +n_sid+ "\n")
    f_config.write("set access address-assignment pool " +n_pool+ "  family inet dhcp-attributes domain-name telecom.kz\n")
    f_config.write("set access address-assignment pool " +n_pool+ "  family inet dhcp-attributes name-server 95.56.237.24\n")
    f_config.write("set access address-assignment pool " +n_pool+ "  family inet dhcp-attributes name-server 212.154.163.162\n")
    f_config.write("set access address-assignment pool " +n_pool+ "  family inet dhcp-attributes router " +n_lo+ "\n")
    f_config.write("set interfaces lo0 unit 0 family inet address " +n_lo+"/32\n")
    f_config.write("set routing-options static route " +net+ " discard\n")
    f_config.write("set policy-options policy-statement SEND-DHCPPOOL-BGP term 1 from route-filter " +net+ " exact\n")

    f_config.close()


def del_config (f_stat,min_pool_name,d_net,prev_pool_name,min_link_name):
    d_conf = re.split('\.', f_stat.strip())
    d_lo = str(IPNetwork(d_net).network+1)
    dhostname_conf = 'data/set_conf/' +str(d_conf[0])+ '.conf'
    d_config = open(dhostname_conf, "w")

    d_config.write("delete access address-assignment pool " +min_pool_name+ "\n")
    d_config.write("delete interfaces lo0 unit 0 family inet address " +d_lo+"/32\n")
    d_config.write("delete routing-options static route " +d_net+ "\n")
    d_config.write("delete policy-options policy-statement SEND-DHCPPOOL-BGP term 1 from route-filter " +d_net+ " exact\n")

    # Check first pool or not
    if not str('NULL') in prev_pool_name:
        d_config.write("delete access address-assignment pool " +prev_pool_name+ " link " +min_pool_name+ "\n")

        #Check last (linked pool) or not
        if not str('NULL') in min_link_name:
            d_config.write("set access address-assignment pool " +prev_pool_name+ " link " +min_link_name+ "\n")
        if str('NULL') in min_link_name:
            print ("It was the last pool in chain...")

    if str('NULL') in prev_pool_name:
        print ("WARNING!!! Somthing wrong! It was the first pool in chain...")

    d_config.close()


def chek_prev_link (min_pool_name,d_stat):
    prev_pool = 'NULL'
    tree = etree.parse('data/stat/' +d_stat)
    root = tree.getroot()
    datadict = []
    for item in root:
        d = {}
        for elem in item:
            d[elem.tag]=elem.text
        datadict.append(d)

        link_match = 'link-name' in d
        if link_match:
#            if str(min_pool_name) in str(d['link-name']):
            if re.search(r'^' +min_pool_name+ '$', str(d['link-name'])):

                prev_pool = str(d['pool-name'])
                print ("PREVIOUS POOL IS:",prev_pool)

#        if not link_match:
#            prev_pool = str('NULL')

    return prev_pool


def check_bngmx(c_stat):
    min_total = int('1000000')
    min_used = int('1000000')
    min_pool_name = str('NULL')
    min_link_name = str('NULL')

    tree = etree.parse('data/stat/' +c_stat)
    root = tree.getroot()
    datadict = []
    n = int('0')
    for item in root:
        d = {}
        n = n + 1
        for elem in item:
            d[elem.tag]=elem.text
        datadict.append(d)

        if n > int('1'):
            if  min_total >= int(d['total-addresses']) and min_used >= int(d['used-addresses']):
                link_match = 'link-name' in d

                if link_match:
                    min_total = int(d['total-addresses'])
                    min_used = int(d['used-addresses'])
                    min_pool_name = str(d['pool-name'])
                    min_link_name = str(d['link-name'])
                if not link_match:
                    min_total = int(d['total-addresses'])
                    min_used = int(d['used-addresses'])
                    min_pool_name = str(d['pool-name'])
                    min_link_name = str('NULL')

            if re.search(r'(all pools in chain)', str(d['pool-name'])):
                max_total = int(d['total-addresses'])
                max_used = int(d['used-addresses'])

    pos_use = int(max_used * 100 / (max_total - min_total))
    if pos_use <= int('97'):
        d_host_file = re.split('\.',c_stat.strip())
        d_hostname = d_host_file[0].strip()
        get_net = 'show configuration access address-assignment pool ' +min_pool_name+ ' family inet network'

        with Device(host=d_hostname, user=USERNAME, password=PASSWORD ) as dev:
            data = dev.rpc.cli (command = get_net)
            d_net = re.search(r'\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\/\d{1,2}', str(etree.tostring(data))).group(0)

#        chek_prev_link (min_pool_name)
        prev_pool_name =  str(chek_prev_link (min_pool_name,c_stat))


        # Get Server ID
        f_in_serv = open("data/const/bngmx.txt", "r")
        sid_lines = f_in_serv.readlines()

        for sid in sid_lines:
            fields_sid = re.split('\|', sid.strip())
            SERVER_NAME = fields_sid[0].strip()
            SERVER_ID = fields_sid[1].strip()

            if re.search(r'^' +n_host+ '$', str(SERVER_NAME)):
                n_sid = str(SERVER_ID)

        f_in_serv.close()

        add_config(n_link, d_net, n_host, n_sid)
        del_config (c_stat,min_pool_name,d_net,prev_pool_name,min_link_name)

    else:
        print ("Impossible to use", f_stat)
        add_impossible(n_host)


# Get statistics
f_input = open("data/const/bngmx.txt", "r")
f_prepare = open("data/const/prepare.txt", "w")
lines = f_input.readlines()

for host in lines:
    fields_host = re.split('\|', host.strip())
    HOSTNAME = fields_host[0].strip()
    SERVER_ID = fields_host[1].strip()
    f_hostname = 'data/stat/' + HOSTNAME + '.xml'

    f_run = open("data/const/run.txt", "w")
    f_run.write ("Getting statistics from " +HOSTNAME+ "...\n")
    f_run.close()


    try:
        dev = Device(host=HOSTNAME, user=USERNAME, password=PASSWORD )
        dev.open()

        # Get last PoolName
        data = dev.rpc.get_config (filter_xml='access/address-assignment/pool/name')

        for elem in data.xpath('//name'):
            p_names = re.sub(r'<n.*?>','',(etree.tostring(elem, encoding='unicode'))).replace('</name>','')
            match = re.search('[A-Z][A-Z][A-Z][A-Z]-BNG-[1-6]',p_names)
            if match:
                atr = p_names.strip()

        # Get Pool Statistics
        f_stat = open(f_hostname, "w")

        data = dev.rpc.get_address_assignment_pool_statistics(get_address_assignment_pool_table_specific = atr)
        p_stat = etree.tostring(data, encoding='unicode')
        f_stat.write(p_stat)
        f_stat.close()

        dev.close()
    except ConnectError as err:
        print ("Can not connect to device: {0}".format(err))
        f_stat.close()


    # GET MIN/MAX USAGE
    try:
        tree = etree.parse(f_hostname)
        root = tree.getroot()

        datadict = []
        for item in root:
            d = {}
            for elem in item:
                d[elem.tag]=elem.text
            datadict.append(d)

            if not re.search(r'link-name', str(d)) and not re.search(r'(all pools in chain)', str(d['pool-name'])):
                link = d['pool-name']

            if re.search(r'(all pools in chain)', str(d['pool-name'])) and int(d['pool-usage']) >= int('98'):

                print (HOSTNAME, d['pool-name'], d['pool-usage'], link, "Need to Add pool")
                f_prepare.write(HOSTNAME+ "|" +link+ "\n")
    except IOError as e:
        print ("File not found")

f_prepare.close()
f_input.close()



f_prepare = open("data/const/prepare.txt", "r")
p_lines = f_prepare.readlines()
for n_conf in p_lines:
    p_fields = re.split('\|', n_conf.strip())
    n_host = p_fields[0].strip()
    n_link = p_fields[1].strip()
    m_host = re.split('-', n_host.strip())


    if re.search(r'.*alma-bngmx-1.*', n_host):
        check_bngmx('alma-bngmx-6.xml')
    if re.search(r'.*alma-bngmx-3.*', n_host):
        check_bngmx('alma-bngmx-4.xml')
    if re.search(r'.*alma-bngmx-4.*', n_host):
        check_bngmx('alma-bngmx-3.xml')
    if re.search(r'.*alma-bngmx-6.*', n_host):
        check_bngmx('alma-bngmx-1.xml')



    files_stat = os.listdir(path="data/stat/")
    for f_stat in files_stat[0:]:
        x_host = str(n_host+ ".xml")

        if re.search(r'.*' +m_host[0]+ '.*', f_stat) and not re.search(r'.*alma.*', f_stat) and not x_host in f_stat:
            check_bngmx(f_stat)

f_prepare.close()


f_run = open("data/const/run.txt", "w")
f_run.write ("Done! " +str(now.year)+ "-" +str(now.month)+ "-" +str(now.day)+ " " +str(now.hour)+ ":" +str(now.minute).zfill(2))
f_run.close()

