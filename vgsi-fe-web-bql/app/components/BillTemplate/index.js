/**
 *
 * BillTemplate
 *
 */

import React from "react";
// import PropTypes from 'prop-types';
// import styled from 'styled-components';

import { FormattedMessage } from "react-intl";
import messages from "./messages";

function BillTemplate() {
  return (
    <div>
      <FormattedMessage {...messages.header} />
    </div>
  );
}

BillTemplate.propTypes = {};

export default BillTemplate;
