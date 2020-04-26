# pip install websocket-client

import websocket, ssl

try:
    import thread
except ImportError:
    import _thread as thread
import time


def on_message(ws, message):
    print(message)


def on_error(ws, error):
    print(error)


def on_close(ws):
    print("### closed ###")


def on_open(ws):
    def run(*args):
        for i in range(1):
            time.sleep(2)
            ws.send("""SUBSCRIBE
 destination:/topic/object-details""")
            time.sleep(.2)
            ws.send("""SUBSCRIBE
 destination:/topic/navTree""")
            time.sleep(.2)
            ws.send("""SUBSCRIBE
 destination:/topic/alarms""")
            time.sleep(.2)
            ws.send("""SUBSCRIBE
 destination:/topic/recent-tasks""")
            time.sleep(.2)
            ws.send("OPEN_INVENTORY:7f2995a5-xxxx-4ba6-886a-xxxxxxxxxxxx")
        time.sleep(1)
        ws.close()
        print("thread terminating...")
    thread.start_new_thread(run, ())


if __name__ == "__main__":
    websocket.enableTrace(True)

    url = "wss://vc.vsphere.local/vsphere-client/endpoints/live-updates?webClientSessionId=52F27394-4001-76FC-3783-XXXXXXXXXXXX"
    ws = websocket.WebSocketApp(url,
                                on_message=on_message,
                                on_error=on_error,
                                on_close=on_close,
                                header={
                                    'Pragma': 'no-cache',
                                    'Cache-Control': 'no-cache',
                                    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36',
                                    'Accept-Encoding': 'gzip, deflate, br',
                                    'Accept-Language': 'tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
                                    'Cookie': 'VSPHERE-USERNAME=administrator%40vsphere.local; VSPHERE-CLIENT-SESSION-INDEX=_e04f5f54XXX88b33e5df1e1f73dXXXXX; JSESSIONID=XXXXXXXX78169498C8F1DDB1D60AXXXXXXX; JSESSIONID=XXXXXXXX9D369093042AF4E57F62C7XXXXX',
                                    'Sec-WebSocket-Extensions': 'permessage-deflate; client_max_window_bits'
                                    }
                                )
    ws.on_open = on_open
    ws.run_forever(
        http_proxy_host="charles.proxy.local",
        http_proxy_port=8888,
        sslopt={"cert_reqs": ssl.CERT_NONE},
    )
