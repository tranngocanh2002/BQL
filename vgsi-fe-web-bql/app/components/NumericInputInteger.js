import React from 'react'

import { Input } from "antd";

export default class NumericInputInteger extends React.Component {
    onChange = e => {
      const { value } = e.target;
      const reg = /^-?(0|[1-9][0-9]*)([0-9]*)?$/;
      if ((!Number.isNaN(value) && reg.test(value)) && Number(value) > 0 || value === '') {
        this.props.onChange(value);
      }
    };
  
    // '.' at the end or only '-' in the input box.
    onBlur = () => {
      const { value, onBlur, onChange } = this.props;
      if (value.charAt(value.length - 1) === '.') {
        onChange(value.slice(0, -1));
      }
      if (onBlur) {
        onBlur();
      }
    };
  
    render() {
      const { value } = this.props;
      return (
        <Input
          {...this.props}
          onChange={this.onChange}
          onBlur={this.onBlur}
        />
      );
    }
  }