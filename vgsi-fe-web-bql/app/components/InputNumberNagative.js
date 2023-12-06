import React from 'react'

import { InputNumber } from "antd";

export default class InputNumberNagative extends React.Component {

    currencyFormatter = selectedCurrOpt => value => {
        return new Intl.NumberFormat("vi").format(value);
      };
      
    currencyParser = val => {
        if(val == "-") {
          return
        }
        try {
          // for when the input gets clears
          if (typeof val === "string" && !val.length) {
            val = "0.0";
          }
      
          // detecting and parsing between comma and dot
          var group = new Intl.NumberFormat("vi").format(1111).replace(/1/g, "");
          var decimal = new Intl.NumberFormat("vi").format(1.1).replace(/1/g, "");
          var reversedVal = val.replace(new RegExp("\\" + group, "g"), "");
          reversedVal = reversedVal.replace(new RegExp("\\" + decimal, "g"), ".");
          //  => 1232.21 €
      
          // removing everything except the digits and dot
          // reversedVal = reversedVal.replace(/[^0-9.]/g, "");
          reversedVal = reversedVal.replace(/[^0-9-]/g, '').replace(/(?!^)-/g, '');
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
    
    render() {
        const { value } = this.props;
        return (
            <InputNumber
                {...this.props}
                value={value}
                formatter={this.currencyFormatter(value)}
                parser={this.currencyParser}
            />
        );
    }
}
