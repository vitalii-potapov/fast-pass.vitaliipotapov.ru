// Source code buttons "quick login"

javascript:(function(){
  const AES_Sbox = [99, 124, 119, 123, 242, 107, 111, 197, 48, 1, 103, 43, 254, 215, 171,
    118, 202, 130, 201, 125, 250, 89, 71, 240, 173, 212, 162, 175, 156, 164, 114, 192, 183, 253,
    147, 38, 54, 63, 247, 204, 52, 165, 229, 241, 113, 216, 49, 21, 4, 199, 35, 195, 24, 150, 5, 154,
    7, 18, 128, 226, 235, 39, 178, 117, 9, 131, 44, 26, 27, 110, 90, 160, 82, 59, 214, 179, 41, 227,
    47, 132, 83, 209, 0, 237, 32, 252, 177, 91, 106, 203, 190, 57, 74, 76, 88, 207, 208, 239, 170,
    251, 67, 77, 51, 133, 69, 249, 2, 127, 80, 60, 159, 168, 81, 163, 64, 143, 146, 157, 56, 245,
    188, 182, 218, 33, 16, 255, 243, 210, 205, 12, 19, 236, 95, 151, 68, 23, 196, 167, 126, 61,
    100, 93, 25, 115, 96, 129, 79, 220, 34, 42, 144, 136, 70, 238, 184, 20, 222, 94, 11, 219, 224,
    50, 58, 10, 73, 6, 36, 92, 194, 211, 172, 98, 145, 149, 228, 121, 231, 200, 55, 109, 141, 213,
    78, 169, 108, 86, 244, 234, 101, 122, 174, 8, 186, 120, 37, 46, 28, 166, 180, 198, 232, 221,
    116, 31, 75, 189, 139, 138, 112, 62, 181, 102, 72, 3, 246, 14, 97, 53, 87, 185, 134, 193, 29,
    158, 225, 248, 152, 17, 105, 217, 142, 148, 155, 30, 135, 233, 206, 85, 40, 223, 140, 161,
    137, 13, 191, 230, 66, 104, 65, 153, 45, 15, 176, 84, 187, 22];
  const AES_ShiftRowTab = [0, 5, 10, 15, 4, 9, 14, 3, 8, 13, 2, 7, 12, 1, 6, 11];

  const AES_Sbox_Inv = new Array(256);
  for (let i = 0; i < 256; i += 1) { AES_Sbox_Inv[AES_Sbox[i]] = i; }

  const AES_ShiftRowTab_Inv = new Array(16);
  for (let i = 0; i < 16; i += 1) { AES_ShiftRowTab_Inv[AES_ShiftRowTab[i]] = i; }

  const AES_xtime = new Array(256);
  for (let i = 0; i < 128; i += 1) {
    AES_xtime[i] = i << 1;
    AES_xtime[128 + i] = (i << 1) ^ 0x1b;
  }

  function AES_ExpandKey(key) {
    const kl = key.length;
    let Rcon = 1;
    let ks;
    switch (kl) {
      case 16: ks = 16 * (10 + 1); break;
      case 24: ks = 16 * (12 + 1); break;
      case 32: ks = 16 * (14 + 1); break;
      default:
        console.log('AES_ExpandKey: Only key lengths of 16, 24 or 32 bytes allowed!');
    }
    for (let i = kl; i < ks; i += 4) {
      let temp = key.slice(i - 4, i);
      const k = key;
      if (i % kl === 0) {
        temp = [AES_Sbox[temp[1]] ^ Rcon, AES_Sbox[temp[2]], AES_Sbox[temp[3]], AES_Sbox[temp[0]]];
        Rcon <<= 1;
        if (Rcon >= 256) { Rcon ^= 0x11b; }
      } else if ((kl > 24) && (i % kl === 16)) {
        temp = [AES_Sbox[temp[0]], AES_Sbox[temp[1]], AES_Sbox[temp[2]], AES_Sbox[temp[3]]];
      }
      for (let j = 0; j < 4; j += 1) { k[i + j] = key[i + j - kl] ^ temp[j]; }
    }
  }
  function AES_AddRoundKey(state, rkey) {
    const s = state;
    for (let i = 0; i < 16; i += 1) { s[i] ^= rkey[i]; }
  }
  function AES_ShiftRows(state, shifttab) {
    const s = state;
    const h = [].concat(state);
    for (let i = 0; i < 16; i += 1) { s[i] = h[shifttab[i]]; }
  }
  function AES_SubBytes(state, sbox) {
    const s = state;
    for (let i = 0; i < 16; i += 1) { s[i] = sbox[s[i]]; }
  }
  function AES_MixColumns_Inv(state) {
    const s = state;
    for (let i = 0; i < 16; i += 4) {
      const s0 = s[i + 0];
      const s1 = s[i + 1];
      const s2 = s[i + 2];
      const s3 = s[i + 3];
      const h = s0 ^ s1 ^ s2 ^ s3;
      const xh = AES_xtime[h];
      const h1 = AES_xtime[AES_xtime[xh ^ s0 ^ s2]] ^ h;
      const h2 = AES_xtime[AES_xtime[xh ^ s1 ^ s3]] ^ h;
      s[i + 0] ^= h1 ^ AES_xtime[s0 ^ s1];
      s[i + 1] ^= h2 ^ AES_xtime[s1 ^ s2];
      s[i + 2] ^= h1 ^ AES_xtime[s2 ^ s3];
      s[i + 3] ^= h2 ^ AES_xtime[s3 ^ s0];
    }
  }
  function AES_Decrypt(block, key) {
    const l = key.length;
    AES_AddRoundKey(block, key.slice(l - 16, l));
    AES_ShiftRows(block, AES_ShiftRowTab_Inv);
    AES_SubBytes(block, AES_Sbox_Inv);
    for (let i = l - 32; i >= 16; i -= 16) {
      AES_AddRoundKey(block, key.slice(i, i + 16));
      AES_MixColumns_Inv(block);
      AES_ShiftRows(block, AES_ShiftRowTab_Inv);
      AES_SubBytes(block, AES_Sbox_Inv);
    }
    AES_AddRoundKey(block, key.slice(0, 16));
  }

  const myElement = document.createElement('div');
  myElement.style.cssText = 'position:fixed;top:0;right:0;left:0;padding:15px 0;background:black;text-align:center;z-index:1000000;box-sizing:border-box;';
  myElement.setAttribute('id', 'secret-key-bar');
  const input = document.createElement('input');
  input.setAttribute('type', 'password');
  input.style.cssText = 'display:inline-block;width:210px;height:34px;padding:6px 12px;font-size:14px;line-height:1;color:rgb(85,85,85);background-color:rgb(255,255,255);background-image:none;border:1px solid rgb(204,204,204);border-radius:4px;margin-left:50px;box-sizing:border-box;font-family:sans-serif!important;';
  input.setAttribute('type', 'password');
  input.setAttribute('placeholder', 'Please enter your secret key');
  input.addEventListener('change', function change() {
    const s = window.location.search;
    const key = this.value;
    const login = s.substring(5, s.indexOf('&pass')).slice(1);
    const pass = s.substring(s.indexOf('&pass'), s.indexOf('&pkey')).slice(6).split(',');
    const publicKey = s.substring(s.indexOf('&pkey'), s.indexOf('&l=')).slice(6);
    const l = s.substring(s.indexOf('&l'), s.indexOf('&attr')).slice(3);
    const privateKey = (key + publicKey).substr(0, 32).split('');
    const privateKeyCharCode = [];
    const attr = s.substring(s.indexOf('&attr')).slice(6).split(',');
    const field_login = attr[1];
    const field_pass = attr[2];
    for (let i = 0; i < privateKey.length; i += 1) {
      privateKeyCharCode.push(privateKey[i].charCodeAt());
    }
    AES_ExpandKey(privateKeyCharCode);
    AES_Decrypt(pass, privateKeyCharCode);
    function bin2String(array) {
      return String.fromCharCode(...array);
    }
    if (attr[0] === '0') {
      document.querySelector(`[name='${field_login}']`).value = login;
      document.querySelector(`[name='${field_pass}']`).value = bin2String(pass.slice(0, l));
    } else {
      document.querySelector(`#${field_login}`).value = login;
      document.querySelector(`#${field_pass}`).value = bin2String(pass.slice(0, l));
    }
  });
  const close = document.createElement('span');
  close.style.cssText = 'display:inline-block;padding:7px 9.7px;margin-left:10px;border:2px solid white;border-radius:3px;color:white;font-weight:700;cursor:pointer;box-sizing:border-box;line-height:1;font-family:sans-serif!important;font-size:16px;';
  close.innerText = 'X';
  close.addEventListener('click', () => {
    document.querySelector('#secret-key-bar').remove();
  });
  myElement.appendChild(input);
  myElement.appendChild(close);
  document.querySelector('body').appendChild(myElement);
  input.focus();
}());
