import argparse
import json
import sys
import urllib.error
import urllib.request

SCAN_URL = 'https://usegeo.blog/wp-json/usegeo/v1/scan'
PUBLIC_URLS = [
    'https://usegeo.blog/?t=cr002-secret',
    'https://usegeo.blog/wp-content/themes/usegeo/assets/js/scanner.js?t=cr002-secret',
]


def post_scan(url='https://example.com'):
    req = urllib.request.Request(
        SCAN_URL,
        data=json.dumps({'url': url}).encode(),
        method='POST',
        headers={'Content-Type': 'application/json', 'Origin': 'https://usegeo.blog'},
    )
    with urllib.request.urlopen(req, timeout=30) as response:
        return response.status, json.loads(response.read().decode())


def verify_fallback():
    status, payload = post_scan()
    assert status == 200, status
    assert payload.get('ok') is True, payload
    assert 'scores' in payload and 'findings' in payload, payload
    assert payload.get('llm_summary', {}).get('available') is False, payload.get('llm_summary')
    print('fallback verification passed')


def verify_llm():
    status, payload = post_scan()
    assert status == 200, status
    assert payload.get('ok') is True, payload
    assert payload.get('llm_summary', {}).get('available') is True, payload.get('llm_summary')
    assert payload.get('llm_summary', {}).get('summary'), payload.get('llm_summary')
    assert payload.get('llm_summary', {}).get('top_recommendations'), payload.get('llm_summary')
    print('llm verification passed')


def verify_secret_scan():
    forbidden_markers = [
        'USEGEO_LLM_API_KEY',
        'Authorization: Bearer',
        'chat.aiprox.net',
    ]
    status, payload = post_scan()
    body = json.dumps(payload)
    for marker in forbidden_markers:
        assert marker not in body, f'forbidden marker found in scan response: {marker}'
    for url in PUBLIC_URLS:
        with urllib.request.urlopen(url, timeout=30) as response:
            text = response.read().decode(errors='replace')
            for marker in forbidden_markers:
                assert marker not in text, f'forbidden marker found in {url}: {marker}'
    print('secret scan passed')


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--mode', choices=['fallback', 'llm', 'secret-scan'], required=True)
    args = parser.parse_args()
    if args.mode == 'fallback':
        verify_fallback()
    elif args.mode == 'llm':
        verify_llm()
    elif args.mode == 'secret-scan':
        verify_secret_scan()


if __name__ == '__main__':
    main()
