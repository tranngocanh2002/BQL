import React from "react";

import { InputNumber } from "antd";
const locale = "vi";

class InputNumberFormat extends React.Component {
  currencyFormatter = (selectedCurrOpt) => (value) => {
    return new Intl.NumberFormat(locale).format(value);
  };

  currencyParser = (val) => {
    try {
      // for when the input gets clears
      if (typeof val === "string" && !val.length) {
        val = "0.0";
      }

      // detecting and parsing between comma and dot
      var group = new Intl.NumberFormat(locale).format(1111).replace(/1/g, "");
      var decimal = new Intl.NumberFormat(locale).format(1.1).replace(/1/g, "");
      var reversedVal = val.replace(new RegExp("\\" + group, "g"), "");
      reversedVal = reversedVal.replace(new RegExp("\\" + decimal, "g"), ".");
      //  => 1232.21 €

      // removing everything except the digits and dot
      reversedVal = reversedVal.replace(/[^0-9.]/g, "");
      //  => 1232.21

      // appending digits properly
      const digitsAfterDecimalCount = (reversedVal.split(".")[1] || []).length;
      const needsDigitsAppended = digitsAfterDecimalCount > 2;

      if (needsDigitsAppended) {
        reversedVal = reversedVal * Math.pow(10, digitsAfterDecimalCount - 2);
      }

      return Number.isNaN(reversedVal) ? 0 : reversedVal;
    } catch (error) {
      console.error(error);
    }
  };

  formatNumber = (value) => {
    let val = `${value}`;
    if (val.length > 1 && val[0] === "0") {
      val = val.slice(1);
    }
    return `${val}`.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  };

  render() {
    const { value, useNew, useDefault, min } = this.props;

    if (useDefault) {
      return <InputNumber min={min || 0} {...this.props} value={value} />;
    }

    if (useNew) {
      return (
        <InputNumber
          min={min || 0}
          {...this.props}
          formatter={this.formatNumber}
          // parser={(value) => value.replace(/\$\s?|(,*)/g, "")}
          value={value}
        />
      );
    }

    return (
      <InputNumber
        min={min || 0}
        {...this.props}
        value={value}
        formatter={this.currencyFormatter(value)}
        parser={this.currencyParser}
      />
    );
  }
}

export default InputNumberFormat;
