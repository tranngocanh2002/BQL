import _, { cloneDeep } from "lodash";
import pathToRegexp from "path-to-regexp";

import { notification } from "antd";
import { PhoneNumberUtil } from "google-libphonenumber";
import moment from "moment";
import _config from "./config";
import { regexEmail } from "./constants";
export { Color } from "./theme";
export const config = _config;

const CHARCODE = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
export function randomVerifyCode(length = 6) {
  let result = "";
  for (let i = 0; i < length; i++) {
    result = `${result}${
      CHARCODE[parseInt(Math.random() * (CHARCODE.length - 1))]
    }`;
  }
  return result;
}

/**
 * Whether the path matches the regexp if the language prefix is ignored, https://github.com/pillarjs/path-to-regexp.
 * @param   {string|regexp|array}     regexp     Specify a string, array of strings, or a regular expression.
 * @param   {string}                  pathname   Specify the pathname to match.
 * @return  {array|null}              Return the result of the match or null.
 */
export function pathMatchRegexp(regexp, pathname) {
  return pathToRegexp(regexp).exec(pathname);
}

/**
 * In an array of objects, specify an object that traverses the objects whose parent ID matches.
 * @param   {array}     array     The Array need to Converted.
 * @param   {string}    current   Specify the object that needs to be queried.
 * @param   {string}    parentId  The alias of the parent ID of the object in the array.
 * @param   {string}    id        The alias of the unique ID of the object in the array.
 * @return  {array}    Return a key array.
 */
export function queryAncestors(array, current, parentId, id = "id") {
  const result = [current];

  // eslint-disable-next-line no-undef
  const hashMap = new Map();
  array.forEach((item) => hashMap.set(item[id], item));
  const getPath = (current) => {
    const currentParentId = hashMap.get(current[id])[parentId];
    if (currentParentId) {
      result.push(hashMap.get(currentParentId));
      getPath(hashMap.get(currentParentId));
    }
  };

  getPath(current);
  return result;
}

/**
 * Add the language prefix in pathname.
 * @param   {string}    pathname   Add the language prefix in the pathname.
 * @return  {string}    Return the pathname after adding the language prefix.
 */
export function addLangPrefix(pathname) {
  return `${pathname}`;
}

export const langFromPath = (pathname) => pathname;
// curry(
//   /**
//    * Query language from pathname.
//    * @param   {array}     languages         Specify which languages are currently available.
//    * @param   {string}    defaultLanguage   Specify the default language.
//    * @param   {string}    pathname          Pathname to be queried.
//    * @return  {string}    Return the queryed language.
//    */
//   (languages, defaultLanguage, pathname) => {
//     for (const item of languages) {
//       if (pathname.startsWith(`/${item}/`)) {
//         return item
//       }
//     }
//     return defaultLanguage
//   }
// )(languages)(defaultLanguage)

/**
 * Convert an array to a tree-structured array.
 * @param   {array}     array     The Array need to Converted.
 * @param   {string}    id        The alias of the unique ID of the object in the array.
 * @param   {string}    parentId       The alias of the parent ID of the object in the array.
 * @param   {string}    children  The alias of children of the object in the array.
 * @return  {array}    Return a tree-structured array.
 */
export function arrayToTree(
  array,
  id = "id",
  parentId = "pid",
  children = "children"
) {
  const result = [];
  const hash = {};
  const data = cloneDeep(array);

  data.forEach((item, index) => {
    hash[data[index][id]] = data[index];
  });

  data.forEach((item) => {
    const hashParent = hash[item[parentId]];
    if (hashParent) {
      !hashParent[children] && (hashParent[children] = []);
      hashParent[children].push(item);
    } else {
      result.push(item);
    }
  });
  return result;
}

export function parseTree(root, gDataList) {
  const loop = (key, iii) => {
    return gDataList
      .filter((node) => node.parent_id == key)
      .map((node) => {
        // if (iii >= config.MAX_LEVEL_CLUSTER_SHOW) return node;
        return {
          ...node,
          children: loop(node.key, iii + 1),
        };
      });
  };
  return [
    {
      name: root.name,
      title: root.name,
      key: "-1",
      value: "-1",
      children: loop(null, 1),
    },
  ];
}

export let isVnPhone = (value) =>
  value &&
  /^(\+84|84|0)(9[0|1|2|3|4|6|7|8|9]|8[1|2|3|4|5|6|8|9]|7[0|6|7|8|9]|5[6|8|9]|3[2|3|4|5|6|7|8|9])([0-9]{7})$/.test(
    value
  );

export function validatePhoneNumber(value) {
  let valid = false;
  try {
    const phoneUtil = PhoneNumberUtil.getInstance();
    valid = phoneUtil.isValidNumber(phoneUtil.parse(value));
  } catch (e) {
    valid = false;
  }
  return valid;
}

export function validateEmail(email) {
  return regexEmail.test(String(email).toLowerCase());
}

export function vietnamCharacters(text) {
  var re =
    /[ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂẾưăạảấầẩẫậắằẳẵặẹẻẽềềểếỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹý]+$/;

  return re.test(String(text).toLowerCase());
}

export function validateName(name) {
  var regexVNCharacter =
    /^[a-zA-ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂẾưăạảấầẩẫậắằẳẵặẹẻẽềềểếỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹý\s]+$/;
  return regexVNCharacter.test(String(name).toLowerCase());
}

export function notificationBar(message, type = "success") {
  notification[type]({
    placement: "bottomRight",
    duration: 3,
    onClose: () => {},
    message: message,
  });
}
export function notificationBar2(message, type = "error") {
  notification[type]({
    placement: "bottomRight",
    duration: 3,
    onClose: () => {},
    message: message,
  });
}
String.prototype.capitalize = function () {
  return this.charAt(0).toUpperCase() + this.slice(1);
};

export const formatNumberToCurrency = (price) => {
  try {
    return price.toLocaleString("vi");
  } catch (e) {
    console.error(e);
  }
};

export const formatNumberToCurrencyOld = (n = 0, toFixed = 2) => {
  let reg = /(\d)(?=(\d{3})+(?:\.\d+)?$)/g;

  let number = parseFloat(n).toFixed(toFixed);
  if (parseInt(n) - number == 0) {
    number = parseInt(n);
  }

  return number.toString().replace(reg, "$&,");
};

var ChuSo = new Array(
  " không ",
  " một ",
  " hai ",
  " ba ",
  " bốn ",
  " năm ",
  " sáu ",
  " bảy ",
  " tám ",
  " chín "
);
var Tien = new Array("", " nghìn", " triệu", " tỷ", " nghìn tỷ", " triệu tỷ");

//1. Hàm đọc số có ba chữ số;
export const DocSo3ChuSo = (baso) => {
  var tram;
  var chuc;
  var donvi;
  var KetQua = "";
  tram = parseInt(baso / 100);
  chuc = parseInt((baso % 100) / 10);
  donvi = baso % 10;
  if (tram == 0 && chuc == 0 && donvi == 0) return "";
  if (tram != 0) {
    KetQua += ChuSo[tram] + " trăm ";
    if (chuc == 0 && donvi != 0) KetQua += " linh ";
  }
  if (chuc != 0 && chuc != 1) {
    KetQua += ChuSo[chuc] + " mươi";
    if (chuc == 0 && donvi != 0) KetQua = KetQua + " linh ";
  }
  if (chuc == 1) KetQua += " mười ";
  switch (donvi) {
    case 1:
      if (chuc != 0 && chuc != 1) {
        KetQua += " mốt ";
      } else {
        KetQua += ChuSo[donvi];
      }
      break;
    case 5:
      if (chuc == 0) {
        KetQua += ChuSo[donvi];
      } else {
        KetQua += " lăm ";
      }
      break;
    default:
      if (donvi != 0) {
        KetQua += ChuSo[donvi];
      }
      break;
  }
  return KetQua;
};
export const DocTienBangChu = (SoTien) => {
  var lan = 0;
  var i = 0;
  var so = 0;
  var KetQua = "";
  var tmp = "";
  var ViTri = new Array();
  if (SoTien < 0) return "Số tiền âm !";
  if (SoTien == 0) return "Không";
  if (SoTien > 0) {
    so = SoTien;
  } else {
    so = -SoTien;
  }
  if (SoTien > 8999999999999999) {
    //SoTien = 0;
    return "Số quá lớn!";
  }
  ViTri[5] = Math.floor(so / 1000000000000000);
  if (isNaN(ViTri[5])) ViTri[5] = "0";
  so = so - parseFloat(ViTri[5].toString()) * 1000000000000000;
  ViTri[4] = Math.floor(so / 1000000000000);
  if (isNaN(ViTri[4])) ViTri[4] = "0";
  so = so - parseFloat(ViTri[4].toString()) * 1000000000000;
  ViTri[3] = Math.floor(so / 1000000000);
  if (isNaN(ViTri[3])) ViTri[3] = "0";
  so = so - parseFloat(ViTri[3].toString()) * 1000000000;
  ViTri[2] = parseInt(so / 1000000);
  if (isNaN(ViTri[2])) ViTri[2] = "0";
  ViTri[1] = parseInt((so % 1000000) / 1000);
  if (isNaN(ViTri[1])) ViTri[1] = "0";
  ViTri[0] = parseInt(so % 1000);
  if (isNaN(ViTri[0])) ViTri[0] = "0";
  if (ViTri[5] > 0) {
    lan = 5;
  } else if (ViTri[4] > 0) {
    lan = 4;
  } else if (ViTri[3] > 0) {
    lan = 3;
  } else if (ViTri[2] > 0) {
    lan = 2;
  } else if (ViTri[1] > 0) {
    lan = 1;
  } else {
    lan = 0;
  }
  for (i = lan; i >= 0; i--) {
    tmp = DocSo3ChuSo(ViTri[i]);
    KetQua += tmp;
    if (ViTri[i] > 0) KetQua += Tien[i];
    if (i > 0 && tmp.length > 0) KetQua += ","; //&& (!string.IsNullOrEmpty(tmp))
  }
  if (KetQua.substring(KetQua.length - 1) == ",") {
    KetQua = KetQua.substring(0, KetQua.length - 1);
  }
  KetQua = KetQua.substring(1, 2).toUpperCase() + KetQua.substring(2);
  return KetQua; //.substring(0, 1);//.toUpperCase();// + KetQua.substring(1);
};

export const parseBillToView = (fees, language) => {
  let feesMap = _.groupBy(fees, "service_map_management_id");
  return Object.keys(feesMap).map((fee) => {
    feesMap[fee].sort((f1, f2) => f1.fee_of_month - f2.fee_of_month);
    let new_money_collected = _.sumBy(
      feesMap[fee],
      (fff) => fff.new_money_collected
    );
    let more_money_collecte = _.sumBy(
      feesMap[fee],
      (fff) => fff.more_money_collecte
    );
    let service_map_management_service_name = "";
    let fee_of_month = "";
    feesMap[fee].forEach((ff, index) => {
      let month = moment.unix(ff.fee_of_month);
      if (index == 0) {
        fee_of_month = month.format("MM");
        service_map_management_service_name =
          language === "en"
            ? ff.service_map_management_service_name_en
            : ff.service_map_management_service_name;
      } else {
        let monthBefore = moment.unix(feesMap[fee][index - 1].fee_of_month);
        if (monthBefore.format("MM/YYYY") !== month.format("MM/YYYY")) {
          fee_of_month = `${fee_of_month}/${monthBefore.format(
            "YYYY"
          )}, ${month.format("MM")}`;
        }
      }

      if (index == feesMap[fee].length - 1) {
        fee_of_month = `${fee_of_month}/${month.format("YYYY")}`;
      }
      if (
        !!feesMap[fee][index - 1] &&
        ff.for_type !== feesMap[fee][index - 1].for_type
      ) {
        service_map_management_service_name = `${service_map_management_service_name}, ${
          language === "en"
            ? ff.service_map_management_service_name_en
            : ff.service_map_management_service_name
        }`;
      }
    });
    return {
      ...feesMap[fee][0],
      service_map_management_service_name,
      fee_of_month,
      more_money_collecte: formatNumberToCurrency(more_money_collecte),
      new_money_collected: formatNumberToCurrency(new_money_collected),
    };
  });
};

// Define the convertNumberToWords function
export function convertNumberToWords(n) {
  if (n < 0) return false;
  var single_digit = [
    "",
    "One",
    "Two",
    "Three",
    "Four",
    "Five",
    "Six",
    "Seven",
    "Eight",
    "Nine",
  ];
  var double_digit = [
    "Ten",
    "Eleven",
    "Twelve",
    "Thirteen",
    "Fourteen",
    "Fifteen",
    "Sixteen",
    "Seventeen",
    "Eighteen",
    "Nineteen",
  ];
  var below_hundred = [
    "Twenty",
    "Thirty",
    "Forty",
    "Fifty",
    "Sixty",
    "Seventy",
    "Eighty",
    "Ninety",
  ];
  if (n === 0) return "Zero";
  function translate(n) {
    var word = "";
    if (n < 10) {
      word = single_digit[n] + " ";
    } else if (n < 20) {
      word = double_digit[n - 10] + " ";
    } else if (n < 100) {
      var rem = translate(n % 10);
      word = below_hundred[(n - (n % 10)) / 10 - 2] + " " + rem;
    } else if (n < 1000) {
      word =
        single_digit[Math.trunc(n / 100)] + " Hundred " + translate(n % 100);
    } else if (n < 1000000) {
      word =
        translate(parseInt(n / 1000)).trim() +
        " Thousand " +
        translate(n % 1000);
    } else if (n < 1000000000) {
      word =
        translate(parseInt(n / 1000000)).trim() +
        " Million " +
        translate(n % 1000000);
    } else {
      word =
        translate(parseInt(n / 1000000000)).trim() +
        " Billion " +
        translate(n % 1000000000);
    }
    return word;
  }
  var result = translate(n);
  return (
    result.trim().slice(0, 1).toUpperCase() +
    result.trim().slice(1).toLowerCase() +
    " "
  );
}

export const parseInvoiceBillToView = (fees) => {
  let feesMap = _.groupBy(fees, "service_map_management_id");
  return Object.keys(feesMap).map((fee) => {
    feesMap[fee].sort((f1, f2) => f1.fee_of_month - f2.fee_of_month);
    let new_money_collected = feesMap[fee].length
      ? formatNumberToCurrency(feesMap[fee][0].money_collected)
      : 0;
    let fee_of_month = "";
    feesMap[fee].forEach((ff, index) => {
      let month = moment.unix(ff.fee_of_month);
      fee_of_month = month.format("MM/YYYY");
    });
    return {
      ...feesMap[fee][0],
      fee_of_month,
      new_money_collected,
    };
  });
};

export const formatPrice = (price) => {
  try {
    return price.toLocaleString("vi");
  } catch (e) {
    console.error(e);
  }
};

export const unescapeHTML = (html) => {
  var escapeEl = document.createElement("textarea");
  escapeEl.innerHTML = html.replace(/(&nbsp;|<([^>]+)>)/gi, "");
  return escapeEl.textContent;
};

// month: DD/YYYY
export const covertStringMonthYearToTime = (month) => {
  let myDate = "01/" + month;
  myDate = myDate.split("/");
  let newDate = new Date(myDate[2], myDate[1] - 1, myDate[0]);
  let result = newDate.getTime() / 1000;
  return result;
};

/**
 * Insert an item into an array at the specified index.
 * @param {*} arr The array to insert into
 * @param {*} index The index at which to insert the item
 * @param  {...any} newItems The item(s) to insert into the array
 * @returns {Array} A new array with the new items inserted
 */
export const insertToArr = (arr, index, ...newItems) => [
  // part of the array before the specified index
  ...arr.slice(0, index),
  // inserted items
  ...newItems,
  // part of the array after the specified index
  ...arr.slice(index),
];
export function validateText(name) {
  var regexVNCharacter =
    /[ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂẾưăạảấầẩẫậắằẳẵặẹẻẽềềểếỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹý]+/;
  return regexVNCharacter.test(String(name));
}
export function validateText2(name) {
  var regexVNCharacter =
    /[ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂẾưăạảấầẩẫậắằẳẵặẹẻẽềềểếỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹý ]+/;
  return regexVNCharacter.test(String(name));
}
export function validateNum(name) {
  var regexVNCharacter = /^\d{0,5}\.{0,1}\d{0,2}$/;
  return regexVNCharacter.test(String(name));
}
export function addressValidate(text) {
  var re =
    /^[a-zA-Z0-9ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂẾưăạảấầẩẫậắằẳẵặẹẻẽềềểếỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹý ,./\-:]+$/;

  return re.test(String(text).toLowerCase());
}
export function toLowerCaseNonAccentVietnamese(str) {
  str = str.toLowerCase();
  //     We can also use this instead of from line 11 to line 17
  //     str = str.replace(/\u00E0|\u00E1|\u1EA1|\u1EA3|\u00E3|\u00E2|\u1EA7|\u1EA5|\u1EAD|\u1EA9|\u1EAB|\u0103|\u1EB1|\u1EAF|\u1EB7|\u1EB3|\u1EB5/g, "a");
  //     str = str.replace(/\u00E8|\u00E9|\u1EB9|\u1EBB|\u1EBD|\u00EA|\u1EC1|\u1EBF|\u1EC7|\u1EC3|\u1EC5/g, "e");
  //     str = str.replace(/\u00EC|\u00ED|\u1ECB|\u1EC9|\u0129/g, "i");
  //     str = str.replace(/\u00F2|\u00F3|\u1ECD|\u1ECF|\u00F5|\u00F4|\u1ED3|\u1ED1|\u1ED9|\u1ED5|\u1ED7|\u01A1|\u1EDD|\u1EDB|\u1EE3|\u1EDF|\u1EE1/g, "o");
  //     str = str.replace(/\u00F9|\u00FA|\u1EE5|\u1EE7|\u0169|\u01B0|\u1EEB|\u1EE9|\u1EF1|\u1EED|\u1EEF/g, "u");
  //     str = str.replace(/\u1EF3|\u00FD|\u1EF5|\u1EF7|\u1EF9/g, "y");
  //     str = str.replace(/\u0111/g, "d");
  str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
  str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
  str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
  str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
  str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
  str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
  str = str.replace(/đ/g, "d");
  // Some system encode vietnamese combining accent as individual utf-8 characters
  str = str.replace(/\u0300|\u0301|\u0303|\u0309|\u0323/g, ""); // Huyền sắc hỏi ngã nặng
  str = str.replace(/\u02C6|\u0306|\u031B/g, ""); // Â, Ê, Ă, Ơ, Ư
  return str;
}

export function translateErrorMessage(mess, lang) {
  // special case where mess has "Code management user" in it
  if (mess.includes("Code management user")) {
    // extract the code
    let code = mess.split(" ")[3];
    return lang === "vi"
      ? `Mã nhân viên ${code} đã tồn tại.`
      : `Employee code ${code} already exist.`;
  }

  switch (lang) {
    case "vi":
      switch (mess) {
        // case "Email không đúng dạng":
        //   return "Email không được để trống";
        // case "Ngày bắt đầu không đúng định dạng":
        //   return "Ngày bắt đầu không được để trống";
        // case "Ngày kết thúc không đúng định dạng":
        //   return "Ngày kết thúc không đúng định dạng";
        // case "Ngày gửi không đúng định dạng":
        //   return "Ngày gửi không được để trống";
        case "Property no head of household":
          return "Bất động sản không có chủ hộ";
        case "New password not equals old password":
          return "Mật khẩu mới không được trùng với mật khẩu cũ";
        case "Cư dân đã được thêm vào căn hộ rồi":
          return "Cư dân đã được thêm vào bất động sản rồi";
        case "Không còn căn hộ thuộc trạng thái nhắc nợ này":
          return "Không còn bất động sản thuộc trạng thái nhắc nợ này";
        case "Không có chủ hộ":
          return "Bất động sản không có chủ hộ";
        case "Auth Group ID không được để trống.":
          return "Nhóm quyền không được để trống.";
        case "Số điện thoại đã tồn tại":
          return "Số điện thoại đã tồn tại trong hệ thống";
        case "Căn hộ chưa được cấu hình phí":
          return "Bất động sản chưa được khai báo sử dụng";
        default:
          return mess;
      }
    case "en":
      switch (mess) {
        case "Email không được để trống":
          return "Email cannot be blank";
        case "Ngày sinh không được để trống":
          return "Birthday cannot be blank";
        case "Ngày nhập khẩu không hợp lệ":
          return "Invalid date of moving in";
        case "Ngày bắt đầu không đúng định dạng":
          return "Start date cannot be blank";
        case "Ngày kết thúc không đúng định dạng":
          return "End date cannot be blank";
        case "Bất động sản không tồn tại trên hệ thống hoặc chưa có chủ hộ":
          return "Property does not exist on the system or has no owner";
        case "Ngày gửi không đúng định dạng":
          return "Send date cannot be blank";
        case "Ngày chốt không được để trống":
          return "Lock date cannot be blank";
        case "Chỉ số chốt không được để trống":
          return "Lock index cannot be blank";
        case "Phí của tháng không được để trống":
          return "Monthly fees cannot be blank";
        case "Ngày chốt không đúng định dạng":
          return "Lock date is not in correct format";
        case "Chỉ số tiêu thụ không được để trống":
          return "Consumption index cannot be blank";
        case "Phí của tháng không đúng định dạng":
          return "Monthly fees is not in correct format";
        case "Chỉ số tiêu thụ phải nhỏ hơn chỉ số chốt":
          return "Consumption index must be less than lock index";
        case "Chỉ số tiêu thụ không đúng định dạng":
          return "Consumption index is not in correct format";
        case "Property no head of household":
          return "Property does not have head of household";
        case "New password not equals old password":
          return "New password must be different from old password";
        case "Cư dân đã được thêm vào căn hộ rồi":
          return "Residents have been added to the apartment already";
        case "Không còn căn hộ thuộc trạng thái nhắc nợ này":
          return "No more apartments belong to this reminder status";
        case "Auth Group ID cannot be blank.":
          return "Authorization group cannot be blank.";
        case "Loại bất động sản không được để trống":
          return "Property type cannot be blank";
        case "Số điện thoại đã tồn tại":
          return "Phone number already exists in the system";
        case "The apartment has not been configured fee":
          return "Property has not been declared for use";
        default:
          return mess;
      }
    default:
      return mess;
  }
}
