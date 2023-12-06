/**
 *
 * PrintButton
 *
 */

import React from "react";
// import PropTypes from 'prop-types';
// import styled from 'styled-components';

import { FormattedMessage } from "react-intl";
import messages from "./messages";
import { Button, message } from "antd";

/* eslint-disable react/prefer-stateless-function */
class PrintButton extends React.PureComponent {
  state = {
    loading: false
  }

  componentWillMount() {
    this.isMouted = true
  }

  componentWillUnmount() {
    this.isMouted = false
  }


  removeWindow = (target) => {
    setTimeout(() => {
      target.parentNode.removeChild(target);
    }, 500);
  };

  _onClick = async () => {
    const { fetchContentToPrint, onBeforePrint, onAfterPrint } = this.props
    if (!!!fetchContentToPrint) {
      message.warning('In không thành công.')
      return
    }
    try {
      this.setState({ loading: true })
      let res = await fetchContentToPrint()
      if (!this.isMouted)
        return
      if (res.success) {
        const printWindow = document.createElement("iframe");
        printWindow.style.position = "absolute";
        printWindow.style.top = "-1000px";
        printWindow.style.left = "-1000px";
        document.body.appendChild(printWindow);
        printWindow.onload = () => {
          console.log(`onLoad`)
        }

        const domDoc = printWindow.contentDocument || printWindow.contentWindow.document;
        domDoc.open();
        domDoc.write(`${res.data.content_html}`);
        domDoc.close();

        if (onBeforePrint) {
          onBeforePrint();
        }

        setTimeout(() => {
          printWindow.contentWindow.focus();
          printWindow.contentWindow.print();
          this.removeWindow(printWindow);

          if (onAfterPrint) {
            onAfterPrint();
          }
          this.setState({ loading: false })
        }, 500);
      } else {
        this.setState({ loading: false })
      }
    } catch (error) {
      console.log(`error`, error)
      this.setState({ loading: false })
      message.warning('In không thành công.')
    }
  }

  render() {
    const { fetchContentToPrint, children, ...rest } = this.props
    const { loading } = this.state
    return (
      <Button {...rest}
        onClick={this._onClick}
        loading={loading}
      >
        {children}
      </Button>
    );
  }
}

PrintButton.propTypes = {};
PrintButton.defaultProps = {
  icon: 'printer',
  type: 'primary',
  ghost: true
}

export default PrintButton;
